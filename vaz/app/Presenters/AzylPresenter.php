<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\Datagrids\AnimalsDatagridFactory;
use App\Components\Datagrids\NewsDatagridFactory;
use App\Forms\animalFormFactory;
use App\Forms\azylSetingsFormFactory;
use App\Forms\collectionFormFactory;
use App\Forms\messagesFormFactory;
use App\Forms\newsFormFactory;
use App\Model\Orm\Entity\Animal;
use App\Model\Orm\Entity\Collections;
use App\Model\Orm\Entity\News;
use App\Model\Orm\Entity\Photo;
use App\Model\Orm\Repository\AdoptionsRepository;
use App\Model\Orm\Repository\AnalyticsRepository;
use App\Model\Orm\Repository\AnimalsRepository;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\CollectionsRepository;
use App\Model\Orm\Repository\MessagesRepository;
use App\Model\Orm\Repository\NewsRepository;
use App\Model\Orm\Repository\PhotosRepository;
use App\Model\Orm\Repository\UsersRepository;
use App\Model\Services\Menu;
use App\Repository\SpeciesRepository;
use App\Services\AnalyticsService;
use App\Services\CollectionKeyService;
use App\Services\MessagesService;
use Contributte\Application\UI\BasePresenter;
use DateTimeImmutable;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Nepada\PhoneNumberDoctrine\PhoneNumberType;
use Nette\Forms\Form;
use Random\RandomException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridColumnStatusException;
use Ublaboo\DataGrid\Exception\DataGridException;

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
                                private messagesFormFactory $messagesFormFactory,
                                private messagesService $messagesService,
                                private AnalyticsRepository $analyticsRepository,
                                private AnalyticsService $analyticsService,
                                private AdoptionsRepository $adoptionsRepository,
                                private CollectionsRepository $collectionsRepository,
                                private collectionFormFactory $collectionFormFactory,
                                private collectionKeyService $collectionKeyService)
    {
        $this->animalsRepository = $animalsRepository;
        $this->animalFormFactory = $animalFormFactory;
        $this->azylSetingsFormFactory = $azylSetingsFormFactory;
        $this->collectionFormFactory = $collectionFormFactory;
        $this->messagesRepository = $messagesRepository;
        $this->speciesRepository = $speciesRepository;
        $this->newsRepository = $newsRepository;
        $this->newsFormFactory = $newsFormFactory;
        $this->newsDatagridFactory = $newsDatagridFactory;
        $this->azylRepository = $azylRepository;
        $this->messagesFormFactory = $messagesFormFactory;
        $this->messagesService = $messagesService;
        $this->analyticsRepository = $analyticsRepository;
        $this->analyticsService = $analyticsService;
        $this->adoptionsRepository = $adoptionsRepository;
        $this->collectionsRepository = $collectionsRepository;
        $this->collectionKeyService = $collectionKeyService;
        parent::__construct();
    }

    public function startup(): void
    {
        parent::startup();
        if (!$this->getPresenter()->getUser()->loggedIn) {
            $this->redirect('Home:SignIn');
        } else {
            if (!($this->getPresenter()->getUser()->isInRole('azyl') || $this->getPresenter()->getUser()->isInRole('superadmin'))) {
                $this->flashMessage('Nemáte dostatečná oprávnění pro tuto akci. Akce byla zalogována!', 'alert-danger');
                $this->analyticsService->setPresenter($this);
                $this->analyticsService->setComment('Azyl presenter, nepovolený přístup|'.$this->getPresenter()->getAction().' | '.$this->getPresenter()->getUser()->getIdentity()->getId());
                $this->analyticsService->logVisit();
                $this->redirect('Home:default');
            } else {
                if (!is_null($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl'])) {

                    $menu = new Menu();
                    $this->getTemplate()->mainMenuItems = $menu->getMenu();
                    $this->analyticsService->setPresenter($this);
                    $this->analyticsService->setComment('Azyl presenter |'.$this->getPresenter()->getAction().' | '.$this->getPresenter()->getUser()->getIdentity()->getId());
                    $this->analyticsService->logVisit();
                }else{
                    $this->getPresenter()->redirect('SuperAdmin:SetAzyl');
                }
            }
        }
    }

    protected function beforeRender(): void
    {
        $this->template->addFilter('safeHtml', function (string $html): string {
            $allowedTags = ['b', 'i', 'a', 'p', 'br'];
            $html = strip_tags($html, '<' . implode('><', $allowedTags) . '>');

            // Povolit pouze bezpečné atributy v <a>
            return preg_replace_callback('/<a\s+([^>]+)>/i', function ($matches) {
                if (preg_match('/href=["\'](.*?)["\']/', $matches[1], $hrefMatch)) {
                    return '<a href="' . htmlspecialchars($hrefMatch[1], ENT_QUOTES) . '">';
                }
                return '<a>';
            }, $html);
        });

    }



    public function handleDelete(int $id): void
    {
        $animal = $this->animalsRepository->findById($id);
        $animal->setIsDeleted(true);
        $this->animalsRepository->saveAnimal($animal);
        $this->flashMessage('Zvířátko bylo smazáno.', 'alert-success');
        $this->redirect('this');
    }

    public function handleStopAdoption(int $id): void
    {

    }

    public function handleEndAdoption(int $id): void
    {

    }



    public function renderDefault(): void
    {
        $this->getTemplate()->title = 'Azyl';

        if ($this->getPresenter()->getUser()->getRoles()[0] === 'superadmin') {
            $this->getTemplate()->countAnimals = $this->animalsRepository->countByAzyl($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
            $this->getTemplate()->countVisits = $this->analyticsRepository->countVisitsForAzyl($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
            $this->getTemplate()->visitors = $this->analyticsRepository->getVisitorsForAzyl($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
        } else {
            $this->getTemplate()->countAnimals = $this->animalsRepository->countByAzyl($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
            $this->getTemplate()->countVisits = $this->analyticsRepository->countVisitsForAzyl($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
            $this->getTemplate()->visitors = $this->analyticsRepository->getVisitorsForAzyl($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
        }



      //  $this->template->newUsersCount = $this->usersRepository->CountNewUsers();
      //  $this->template->usersCount = $this->usersRepository->CountUsers();
      //  $this->template->azylsCount = $this->usersRepository->CountAzyls();
    }

    public function renderAnimals(): void
    {
        $this->template->title = 'Animals';
    }

    public function renderCollection(): void
    {
        $this->getTemplate()->title = 'Collection';
    }

    public function actionCollections(?int $key = null): void
    {
        $this->getTemplate()->collections = $this->collectionsRepository->findByAzylActive($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
    }

    public function handleStopCollection(int $key): void
    {
    bdump('STOOP');
    }

    public function handleCollectionPayments(int $key): void
    {
        bdump('platby');
        $this->getTemplate()->payments = $this->collectionsRepository->findOneByKey($key)->getPayments();
        if($this->isAjax())
        {
            $this->getPresenter()->redrawControl('payments-'.$key);

        }
    }

    public function createComponentCollectionForm(): Form
    {
        $form = $this->collectionFormFactory->create();
        $form->onSuccess[] = [$this, 'collectionFormSucceeded'];

            if (is_null($this->getPresenter()->getParameter('key')))
            {
            return $form;
            }
            else
            {
                $collection = $this->collectionsRepository->findOneByKey(intval($this->getPresenter()->getParameter('key')));
                $form->setDefaults([
                    'collectionName' => $collection->getCollectionName(),
                    'collectionId' => $collection->getId(),
                    'collectionDescription' => $collection->getCollectionDescription(),
                    'minimalAmount' => $collection->getMinimalAmount() ?? 50,
                    'resultAmount' => $collection->getResultAmount(),
                    'extendedAmount' => $collection->getExtendedAmount() ?? 0,
                    'startAt' => $collection->getStartAt(),
                    'endingAt' => $collection->getEndingAt(),
                    'extendTo' => $collection->getExtendTo(),
                    'currency' => $collection->getCurrency(),
                    'extend' => 'true',
                    'isActive' => $collection->isActive()

                ]);
                return $form;
            }
    }

    /**
     * @throws \DateMalformedStringException
     * @throws RandomException
     */
    public function collectionFormSucceeded(Form $form, $values): void
    {
        if (is_null($this->getPresenter()->getParameter('key')))
        {
            $azyl  = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
            $user = $this->usersRepository->findOneBy(['id' =>$this->getPresenter()->getUser()->getIdentity()->getData()['User']->getId()]);
            $collection = new Collections();
            $collection->setAzyl($azyl);
            $collection->setCurrency($values['currency']);
            $collection->setCollectionDescription($values['collectionDescription']);
            $collection->setCollectionName($values['collectionName']);
            $collection->setMinimalAmount($values['minimalAmount']);
            $collection->setResultAmount($values['resultAmount']);
            $collection->setExtendedAmount($values['extendedAmount']);
            $collection->setCreatedAt(new DateTimeImmutable('now'));
            $collection->setEndingAt($values['endingAt']);
            $collection->setUser($user);
            $collection->setStartAt($values['startAt']);
            $collection->setIsActive($values['isActive']);
            $this->collectionsRepository->save($collection);
            $collection->setCollectionKey($this->collectionKeyService->createCollectionKey($azyl->getId(),$collection->getId()));

            $photo = new Photo();
            $photo->setAzyl($azyl);
            $photo->setCollections($collection);
            $photo->setUser($user);
            $photo->setDate(new DateTimeImmutable());
            $photo->uploadCollectionHeadlinePhoto($values['headline']);

            $this->photosRepository->save($photo);

            $collection->setPhoto($photo);
            $this->collectionsRepository->save($collection);
            $this->flashMessage('Sbírka byla uložena, pokud je datum nastavené na dnešek ihned se spustí', 'alert-success');
            $this->getPresenter()->redirect('Azyl:Collections');
        }
        else
        {
            $azyl  = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
            $user = $this->usersRepository->findOneBy(['id' =>$this->getPresenter()->getUser()->getIdentity()->getData()['User']->getId()]);
            $collection = $this->collectionsRepository->findOneByKey($this->getPresenter()->getParameter('key'));
            $collection->setAzyl($azyl);
            $collection->setCurrency($values['currency']);
            $collection->setCollectionDescription($values['collectionDescription']);
            $collection->setCollectionName($values['collectionName']);
            $collection->setMinimalAmount($values['minimalAmount']);
            $collection->setResultAmount($values['resultAmount']);
            $collection->setExtendedAmount($values['extendedAmount']);
            $collection->setCreatedAt(new DateTimeImmutable('now'));
            $collection->setEndingAt($values['endingAt']);
            $collection->setUser($user);
            $collection->setStartAt($values['startAt']);
            $collection->setIsActive($values['isActive']);
            $this->collectionsRepository->save($collection);
            $collection->setCollectionKey($this->collectionKeyService->createCollectionKey($azyl->getId(),$collection->getId()));

            $photo = new Photo();
            $photo->setAzyl($azyl);
            $photo->setCollections($collection);
            $photo->setUser($user);
            $photo->setDate(new DateTimeImmutable());
            $photo->uploadCollectionHeadlinePhoto($values['headline']);

            $this->photosRepository->save($photo);

            $collection->setPhoto($photo);
            $this->collectionsRepository->save($collection);
            $this->flashMessage('Sbírka byla uložena, pokud je datum nastavené na dnešek ihned se spustí', 'alert-success');
            $this->getPresenter()->redirect('Azyl:Collections');
        }
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
                if($animal->getAdoption())
                {
                    $this->getTemplate()->adoptions = $animal->getAdoption();
                }
                $this->getTemplate()->title = 'Azyl - Editace zvířátka';
                $this['animalForm']->setDefaults($animal);
            }
        }
    }

    public function actionMessages($id): void
    {
        $this->template->title = 'Zprávy';
        $recieverUser = $this->usersRepository->findOneBy(['azyl'=>$this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']]);
        $messages =  $this->messagesService->getUserContacts($recieverUser->getId());
        foreach($messages as $message)
        {
            $chats[$message->getSenderAddress()] = $message->getSender()->getUsername();
        }
        $this->template->chats = $chats;
        $this->redrawControl('chats');
        $this->redrawControl('messagesCount');
        $this->redrawControl('messages');
    }

    public function actionAdoptions(?int $id): void
    {

        if (!is_null($id))
        {
            $adoption = $this->adoptionsRepository->findOneBy(['id'=>$id]);
            $this->getTemplate()->adoption = $adoption;

        }
         else
        {

            $adoptions = $this->adoptionsRepository->findBy(['azyl' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']],['updatedAt'=>'DESC']);

            $this->getTemplate()->adoptions = $adoptions;
        }

    }


    public function handleChat(string $id): void
    {
        $recieverUser = $this->usersRepository->findOneBy(['azyl'=>$this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']]);
        $messages = $this->messagesRepository->getMessagesBySenderReceiverAddress(senderAddress: $id, receiverAddress: $recieverUser->getMessageAddress());

        $this->getTemplate()->messages = $messages;
        $this->getTemplate()->receiver = $recieverUser->getId();
        $this->messagesService->markMessagesAsRead($id);
        $this->redrawControl('messagesCount');
        $this->redrawControl('chats');
        $this->redrawControl('messages');
    }

    public function handleDeleteMsg(int $id): void
    {
        $redirectAddress = $this->messagesRepository->getMessagesById($id)->getReceiverAddress();
        if($this->messagesService->deleteMessage($id,$this->getPresenter()))
        {
            $this->flashMessage('Vzkaz byl smazán.', 'alert-success');
        } else {

            $this->flashMessage('Při mazání vzkazu nastala chyba.', 'alert-danger');
        }
        if($this->isAjax()){
            $this->redrawControl('messagesCount');
            $this->redrawControl('chats');
            $this->redrawControl('messages');
        }
        else {
            $chat = "?do=chat";
            $url = $this->link('User:messages', $redirectAddress) . $chat;
            $this->redirectUrl($url);
        }

    }

    public function messagesFormSucceeded(\Nette\Application\UI\Form $form, \stdClass $values) : void
    {
        $this->getPresenter()->isAjax();
        $this->messagesService->messagesFormSucceeded($form, $values, $this->getPresenter());

        /*
        $message = New Messages();
        $message->setSender($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
        $message->setType(MessageTypeEnum::FROMUSER_TYPE);
        $message->setReceiver($this->usersRepository->getUserById(intval($values->id)));
        $message->setMessage($values->message);
        $message->setCreatedAt(new DateTimeImmutable());
        $message->setReaded(false);
        $this->messagesRepository->save($message);
        */
        if($this->isAjax()){
            $this->redrawControl('messages');
        }
        else {
            $chat = "?do=chat";
            $url = $this->link('this', $values->id) . $chat;
            $this->redirectUrl($url);

        }

    }
    public function actionNews(): void
    {
        $this->template->title = 'News';
    }

    public function renderPhotos(): void //TODO: Zobrazení Fotek azylu
    {
        $this->template->title = 'Photos';
        $this->getTemplate()->basepath = '';
        $this->getTemplate()->photos = $this->photosRepository->fetchByAzylId($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
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
        $this->template->title = 'Adoptions - Azyl: '.$this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getAzylName();


    }

    // Actions

    // Handle
    public function handleDeletePhoto(int $id): void
    {

    }
    public function handleSetHomePhoto(int $id): void
    {

    }


    public function handleNewsDelete(?int $id): void
    {
        $news = $this->newsRepository->findOneBy(['id' => $id]);
        if ($news === null) {
            $this->flashMessage('Novinka nebyla nalezena.', 'alert-warning');
            if ($this->isAjax()) {
                $this->redrawControl('flashes');
                $this['actionsGrid']->reload();

            } else {
                $this->redirect('Azyl:news');
            }
        } else {

            $news->setDeleted(true);
            $this->newsRepository->save($news);
            $this->flashMessage('Novinka byla smazána.', 'alert-success');
            if ($this->isAjax()) {
                $this->redrawControl('flashes');
                $this['actionsGrid']->reload();

            } else {
                $this->redirect('Azyl:news');
            }
        }
    }

    // Components

    public function createComponentAnimalForm(): Form
    {
        $form = $this->animalFormFactory->create();
        $form->onSuccess[] = [$this, 'animalFormSucceeded'];
        if ($this->getPresenter()->getParameter('id') !== null){
            $animal = $this->animalsRepository->findById(intval($this->getPresenter()->getParameter('id')));

            $form->setDefaults([
               'name' => $animal->getName(),
                'description' => $animal->getDescription(),
                'species' => $animal->getSpecies()->getId(),
                    'birthDate' => $animal->getBirthdate()->format('d-m-Y'),
                    'breed' => $animal->getBreed(),
                    'toAdoption' => $animal->isToAdoption(),
                    'adoptionType' => $animal->getAdoptionType(),
                    'multiAdoption' => $animal->getMultiAdoption(),
                    'howMuch' => $animal->getHowMuch()]
                    );


        }


        return $form;
    }

    public function createComponentAzylSettingsForm(): Form
    {
        $formDefaults = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
        $form = $this->azylSetingsFormFactory->create();
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
        if (is_null($id))
        {
            
            $animal = New Animal();
            $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());

            $animal->setAzyl($azyl);
            $animal->setIsDeleted(false);
            $animal->setAdopted(false);
            $animal->setToAdoption($values->toAdoption);
            $animal->setAdoptionType($values->adoptionType);
            $animal->setName($values->name);
            $animal->setDescription($values->description);
            $animal->setSpecies($this->speciesRepository->findOneById($values->species));
            $animal->setBirthDate($values->birthDate);
            $animal->setBreed($values->breed);
            $animal->setHowMuch($values->howMuch);
            $animal->setMultiAdoption($values->multiAdoption);
            $this->animalsRepository->persist($animal);
            foreach ($values->photos as $photo)
            {

                $photoUpload = New Photo();
                $photoUpload->setAzyl($azyl);
                $photoUpload->setDate(new DateTimeImmutable('now'));
                $photoUpload->setAnimal($animal);
                $photoUpload->uploadAzylPhoto($photo);
                $this->photosRepository->save($photoUpload);
            }
            $this->animalsRepository->flush($animal);
            $this->flashMessage('Zvířátko bylo úspěšně přidáno.', 'alert-success');
            $this->redirect('azyl:animals');
        }
        else
        {

            $animal = $this->animalsRepository->findById(intval($id));
            $animal->setName($values->name);
            $animal->setDescription($values->description);
            $animal->setSpecies($this->speciesRepository->findOneById(intval($values->species)));
            $animal->setBirthDate($values->birthDate);
            $animal->setBreed($values->breed);
            $animal->setToAdoption($values->toAdoption);
            $animal->setAdoptionType($values->adoptionType);
            $animal->setHowMuch($values->howMuch);
            $animal->setMultiAdoption($values->multiAdoption);

            foreach ($values->photos as $photo)
            {
                $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
                $photoUpload = New Photo();
                $photoUpload->setAnimal($animal);
                $photoUpload->setDate(new DateTimeImmutable('now'));
                $photoUpload->setAzyl($azyl);
                $photoUpload->uploadAzylPhoto($photo);
                $this->photosRepository->save($photoUpload);
            }

            $this->animalsRepository->saveAnimal($animal);
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
        } else {

        $form->onSuccess[] = [ $this, 'newsFormSucceeded'];

        return $form;
        }
    }
    public function createComponentMessagesForm(): Form
    {
        $form = $this->messagesFormFactory->create();
        $form->onSuccess[] = [$this, 'messagesFormSucceeded'];
        return $form;
    }

    public function newsFormSucceeded(Form $form, \stdClass $values): void
    {

            $news = new News();
            $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getIdentity()->getId());

            $azyl = $this->azylRepository->findOneBy(['id' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId()]);

            $news->setAuthor($user);
            $news->setTitle($values->title);
            $news->setContent($values->content);
            $news->setGlobal($values->global);
            $news->setVisibleFrom($values->visibleFrom);
            $news->setImportant($values->important);
            $news->setCreatedAt(new DateTimeImmutable());
            $news->setDeleted(false);
            $news->setAzyl($azyl);

            $this->newsRepository->save($news);


            $this->flashMessage('Novinka byla uložena.', 'alert-success');
            $this->redirect('Azyl:news');

    }

    public function newsFormSucceededUpdate(Form $form, \stdClass $values): void
    {
        $id = $this->getPresenter()->getParameter('id');
        if ($id !== null)  {
            $news = $this->newsRepository->findOneBy(['id' => $id]);

            if ($news) {
                $azyl = $this->azylRepository->findOneBy(['id' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId()]);

                $news->setTitle($values->title);
                $news->setContent($values->content);
                $news->setGlobal($values->global);
                $news->setVisibleFrom($values->visibleFrom);
                $news->setUpdatedAt(new DateTimeImmutable());
                $news->setImportant($values->important);
                $news->setAzyl($azyl);

                $this->newsRepository->save($news);

                $this->flashMessage('Novinka byla aktualizována.', 'alert-success');
                $this->redirect('Azyl:news');
            }
        }
    }


    /**
     * @throws DataGridColumnStatusException
     * @throws DataGridException
     */
    public function createComponentNewsDatagrid(): DataGrid
    {
        $grid = new NewsDatagridFactory($this->newsRepository);
        $grid->setPresenter($this->getPresenter());
        $grid->create(); // upraví instanci, nepřepíše ji novým objektem
        $azyl = $this->azylRepository->findOneBy(['id' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId()]);

        $grid ->setDataSource($azyl->getNews());
        return $grid;
    }

    public function createComponentAnimalsAzylDatagrid(): DataGrid

    {     $grid = new AnimalsDatagridFactory($this->animalsRepository);
            $grid->setPresenter($this->getPresenter());

         $grid->create(); // upraví instanci, nepřepíše ji novým objektem
           $grid->setDataSource($this->animalsRepository->findBy(['azyl' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']]));
           return $grid;

    }
}