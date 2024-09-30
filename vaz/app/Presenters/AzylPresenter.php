<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\Datagrids\AnimalsDatagridFactory;
use App\Components\Datagrids\NewsDatagridFactory;
use App\Forms\animalFormFactory;
use App\Forms\azylSetingsFormFactory;
use App\Forms\messagesFormFactory;
use App\Forms\newsFormFactory;
use App\Model\Orm\Entity\Animal;
use App\Model\Orm\Entity\Messages;
use App\Model\Orm\Entity\News;
use App\Model\Orm\Entity\Photo;
use App\Model\Orm\Enums\MessageTypeEnum;
use App\Model\Orm\Enums\RoleTypeEnum;
use App\Model\Orm\Repository\AnimalsRepository;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\MessagesRepository;
use App\Model\Orm\Repository\NewsRepository;
use App\Model\Orm\Repository\PhotosRepository;
use App\Model\Orm\Repository\UsersRepository;
use App\Model\Services\Menu;
use App\Repository\SpeciesRepository;
use Contributte\Application\UI\BasePresenter;
use DateTimeImmutable;
use Nette\Forms\Form;
use Ublaboo\DataGrid\DataGrid;

class AzylPresenter extends BasePresenter
{
    private AnimalsRepository $animalsRepository;
    private AnimalFormFactory $animalFormFactory;
    private AzylSetingsFormFactory $azylSetingsFormFactory;


    public function __construct(AnimalsRepository      $animalsRepository,
                                AnimalFormFactory      $animalFormFactory,
                                AzylSetingsFormFactory $azylSetingsFormFactory,
                                public NewsRepository  $newsRepository,
                                public NewsFormFactory $newsFormFactory,
                                public NewsDatagridFactory $newsDatagridFactory,
                                public AnimalsDatagridFactory $animalsDatagridFactory,
                                public Photo $photos,
                                public PhotosRepository $photosRepository,
                                public SpeciesRepository $speciesRepository,
                                public UsersRepository $usersRepository,
                                public AzylRepository $azylRepository,
                                private MessagesRepository $messagesRepository,
                                private messagesFormFactory $messagesFormFactory)
    {
        $this->animalsRepository = $animalsRepository;
        $this->animalFormFactory = $animalFormFactory;
        $this->azylSetingsFormFactory = $azylSetingsFormFactory;
        $this->messagesRepository = $messagesRepository;
        $this->speciesRepository = $speciesRepository;
        $this->newsRepository = $newsRepository;
        $this->newsFormFactory = $newsFormFactory;
        $this->newsDatagridFactory = $newsDatagridFactory;
        $this->azylRepository = $azylRepository;
        $this->messagesFormFactory = $messagesFormFactory;
        parent::__construct();
    }

    public function startup(): void
    {

        if (!$this->getPresenter()->getUser()->isLoggedIn()) {
            $this->redirect('Home:SignIn');
        } else {
            if (!($this->getPresenter()->getUser()->isInRole('azyl') || $this->getPresenter()->getUser()->isInRole('superadmin'))) {
                $this->flashMessage('Nemáte dostatečná oprávnění pro tuto akci. Akce byla zalogována!', 'alert-danger');
                $this->redirect('Home:default');
            } else {
                if (!is_null($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl'])) {
                    parent::startup();
                    $menu = new Menu();
                    $this->getTemplate()->mainMenuItems = $menu->getMenu();
                }else{
                    $this->getPresenter()->redirect('SuperAdmin:SetAzyl');
                }
            }
        }
    }

    public function handleDelete(int $id): void
    {
        $animal = $this->animalsRepository->findById($id);
        $animal->setIsDeleted(true);
        $this->animalsRepository->saveAnimal($animal);
        $this->flashMessage('Zvířátko bylo smazáno.', 'alert-success');
        $this->redirect('this');
    }

    public function renderDefault(): void
    {
        $this->template->title = 'Azyl';

        if ($this->getPresenter()->getUser()->getRoles()[0] === 'superadmin') {
            $this->getTemplate()->countAnimals = 999;
        } else {
            $this->getTemplate()->countAnimals = $this->animalsRepository->countByAzyl($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
        }



      //  $this->template->newUsersCount = $this->usersRepository->CountNewUsers();
      //  $this->template->usersCount = $this->usersRepository->CountUsers();
      //  $this->template->azylsCount = $this->usersRepository->CountAzyls();
    }

    public function renderAnimals(): void
    {
        $this->template->title = 'Animals';
    }

    public function actionAnimal(?int $id = null): void
    {
        if ($id === null)
        {
            $this->getTemplate()->title = 'Azyl - Přidání nového zvířátka';
        }
        else {
            if (!$animal = $this->animalsRepository->findById($id)) {
                $this->getTemplate()->title = 'Azyl - Přidání nového zvířátka';

            }
            else
            {   $this->getTemplate()->photos = $animal->getPhotos();
                $this->getTemplate()->title = 'Azyl - Editace zvířátka';
                $this['animalForm']->setDefaults($animal);
            }
        }
    }

    public function actionMessages($id): void
    {
        $this->template->title = 'Zprávy';
        $messages = $this->messagesRepository->getMessagesByReceiverId($this->getPresenter()->getUser()->getId());
        foreach($messages as $message)
        {
            $chats[$message->getSender()->getId()] = $message->getSender()->getUsername();
        }
        $this->redrawControl('chats');
        $this->redrawControl('messages');

        $this->template->chats = $chats;
    }

    public function handleChat(int $id): void
    {
        $messages = $this->messagesRepository->getMessagesBySenderId($id);
        $this->getTemplate()->messages = $messages;
        $this->getTemplate()->reciver = $id;

        $this->redrawControl('chats');
        foreach ($messages as $message)
        {
            $message->setReaded(true);
            $this->messagesRepository->save($message);
        }
        $this->redrawControl('messages');
    }

    public function handleDeleteMsg(int $id): void
    {
        $messages = $this->messagesRepository->getMessagesById($id);
        $redirectId = $messages->getSender()->getId();
        $messages->setDeletedAt(new DateTimeImmutable());
        $this->messagesRepository->save($messages);

        if($this->isAjax()){
            $this->redrawControl('messages');
        }
        else {
            $chat = "?do=chat";
            $url = $this->link('Azyl:messages', $redirectId) . $chat;
            $this->redirectUrl($url);

        }

    }

    public function handleSendMessage($ajax): void
    {
        $this->getPresenter()->isAjax();
        $message = New Messages();
        $message->setSender($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
        $message->setType(MessageTypeEnum::FROMUSER_TYPE);
        $message->setReceiver($this->usersRepository->getUserById(intval($values->id)));
        $message->setMessage($values->message);
        $message->setCreatedAt(new DateTimeImmutable());
        $message->setReaded(false);
        $this->messagesRepository->save($message);
        $this->redrawControl('messages');

    }
    public function actionNews(): void
    {
        $this->template->title = 'News';
    }

    public function renderPhotos(): void
    {
        $this->template->title = 'Photos';
        $this->getTemplate()->basepath = '';
        bdump($this->getTemplate()->photos = $this->photosRepository->fetchByAzylId($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId()));
    }

    public function renderMessages(): void
    {
        $this->template->title = 'Message';
    }

    public function renderSettings(): void
    {
        $this->template->title = 'Settings';
    }

    public function renderAdoptions(): void
    {
        $this->template->title = 'Adoptions';
    }

    // Actions

    // Handle

    // Components

    public function createComponentAnimalForm(): Form
    {
        $form = $this->animalFormFactory->create();
        $form->onSuccess[] = [$this, 'animalFormSucceeded'];
        return $form;
    }

    public function createComponentAzylSettingsForm(): Form
    {
        $formDefaults = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
        $form = $this->azylSetingsFormFactory->create();
        bdump($formDefaults);

        bdump($form);
        $form->onSuccess[] = [$this, 'azylSettingsFormSucceeded'];

        $form->onRender[] = fn() => $form->setDefaults($formDefaults->toArray());
        return $form;
    }

    public function azylSettingsFormSucceeded(Form $form, $values) :void
    {

        $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());

        $azyl->setAzylName($values['azylName']);
        $azyl->setDescription($values['description']);
        $azyl->setBankAccount($values['bankAccount']);
        $azyl->setBankCode($values['bankCode']);
        $azyl->setBankSpecificCode($values['bankSpecificCode']);
        $azyl->setPhoneNumber($values['phoneNumber']);

        $this->azylRepository->saveAzyl($azyl);
        $this->flashMessage('Nastavení azylu bylo aktualizováno.', 'alert-success');
        $this->redirect('this');
    }

    public function animalFormSucceeded(Form $form, $values): void
    {
        $id = $this->getParameter('id');
        //todo: ověření práv uživatele na úpravu zvířátka
        if ($id === null)
        {
            bdump($values);
            
            $animal = New Animal();
            $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
            bdump($azyl);
            $animal->setAzyl($azyl);
            $animal->setIsDeleted(false);
            $animal->setAdopted(false);
            $animal->setToAdoption($values->toAdoption);
            $animal->setName($values->name);
            $animal->setDescription($values->description);
            $animal->setSpecies($this->speciesRepository->findOneById($values->species));
            $animal->setBirthDate($values->birthDate);
            $animal->setBreed($values->breed);
            $this->animalsRepository->persist($animal);
            foreach ($values->photos as $photo)
            {
                $photoUpload = New Photo();
                $photoUpload -> setAzyl($azyl);
                $photoUpload -> setDate(new DateTimeImmutable('now'));
                $photoUpload->uploadAzylPhoto($photo);
                $photoUpload->setAnimal($animal);
                $this->photosRepository->save($photoUpload);
            }
            $this->animalsRepository->flush($animal);
            $this->flashMessage('Zvířátko bylo úspěšně přidáno.', 'alert-success');
        }
        else
        {
            $animal = $this->animalsRepository->findById($id);

            $animal->setName($values->name);
            $animal->setDescription($values->description);
            $animal->setSpecies($values->species);
            $animal->setBirthDate($values->birthDate);
            $animal->setBreed($values->breed);
            $animal->setToAdoption($values->toAdoption);
            foreach ($values->photos as $photo)
            {
                $photoUpload = New Photo();
                $photoUpload->uploadAzylPhoto($photo);
                $photoUpload->setAnimal($animal);
                $this->photosRepository->save($photoUpload);
            }

            $this->animalsRepository->saveAnimal($values);
            $this->flashMessage('Zvířátko bylo úspěšně upraveno.', 'alert-success');
        }
        $this->redirect('this');
    }

    public function createComponentNewsForm(): \Nette\Application\UI\Form
    {
        $form = $this->newsFormFactory->create();

        if ($this->getPresenter()->getParameter('id') !== null) {
            $news = $this->newsRepository->findOneBy(['id' => $this->getPresenter()->getParameter('id')]);
            if ($news) {
                bdump($news);
                $form->addHidden('newsid', $news->getId());
                $form->setDefaults(
                    [
                        'title' => $news->getTitle(),
                        'content' => $news->getContent(),
                        'global' => $news->getGlobal(),
                        'visibleFrom' => $news->getVisibleFrom(),
                        'important' => $news->getImportant(),

                    ]);

                $form->onSuccess[] = [ $this, 'newsFormSucceededUpdate'];
                return $form;

            }
            else
            {
                $this->flashMessage('Novinka nebyla nalezena.', 'alert-danger');
                $this->redirect('Azyl:news');
            }
        }
        $form->onSuccess[] = [ $this, 'newsFormSucceeded'];
        bdump($form, 'Form');
        return $form;
    }
    public function createComponentMessagesForm(): Form
    {
        $form = $this->messagesFormFactory->create();
        $form->onSuccess[] = [$this, 'messagesFormSucceeded'];
        return $form;
    }
    public function messagesFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values) : void
    {
        $message = New Messages();
        $message->setSender($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
        $message->setType(MessageTypeEnum::FROMUSER_TYPE);
        $message->setReceiver($this->usersRepository->getUserById(intval($values->id)));
        $message->setMessage($values->message);
        $message->setCreatedAt(new DateTimeImmutable());
        $message->setReaded(false);
        $this->messagesRepository->save($message);

        if($this->isAjax()){
            $this->redrawControl('messages');
        }
        else {
            $chat = "?do=chat";
            $url = $this->link('Azyl:messages', $values->id) . $chat;
            $this->redirectUrl($url);

        }
        //$this->redirect('this');
    }

    public function newsFormSucceeded(Form $form, \stdClass $values): void
    {

            $news = new News();
            $news->setAuthor($this->usersRepository->getUserById($this->getPresenter()->getUser()->getIdentity()->getId()));
            $news->setTitle($values->title);
            $news->setContent($values->content);
            $news->setGlobal($values->global);
            $news->setVisibleFrom($values->visibleFrom);
            $news->setImportant($values->important);
            $news->setCreatedAt(new DateTimeImmutable());
            $news->setDeleted(false);

            bdump($news, 'Tady nemám do piči bejt');
            $this->newsRepository->save($news);


            $this->flashMessage('Novinka byla uložena.', 'success');
            $this->redirect('Azyl:news');

    }

    public function newsFormSucceededUpdate(Form $form, \stdClass $values): void
    {
        $id = $this->getPresenter()->getParameter('id');
        if ($id !== null)  {
            $news = $this->newsRepository->findOneBy(['id' => $id]);
            bdump($news,'Před if');
            if ($news) {

                $news->setTitle($values->title);
                $news->setContent($values->content);
                $news->setGlobal($values->global);
                $news->setVisibleFrom($values->visibleFrom);
                $news->setUpdatedAt(new DateTimeImmutable());
                $news->setImportant($values->important);
                $this->newsRepository->save($news);
                $this->flashMessage('Novinka byla aktualizována.', 'success');
                $this->redirect('Azyl:news');
            }
        }
    }



    public function createComponentNewsDatagrid(): DataGrid
    {
       // bdump($this->getPresenter()->getUser()->getIdentity()->getData()['User']->getId());
        $grid = $this->newsDatagridFactory->create($this->getPresenter()->getUser()->getIdentity()->getData()['User']->getId());
      //  $grid ->setDataSource($this->getPresenter()->getUser()->getIdentity()->getData()['User']->getNews());
        return $grid;
    }

    public function createComponentAnimalsAzylDatagrid(): DataGrid
    {
            $grid = $this->animalsDatagridFactory->create();
           $grid->setDataSource($this->animalsRepository->findBy(['azyl' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId()]));
           return $grid;

    }
}