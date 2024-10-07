<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\PhotoUploadFormFactory;
use App\Forms\RegisterFormFactory;
use App\Forms\messagesFormFactory;
use App\Forms\roleFormFactory;
use App\Forms\userDetailsFormFactory;
use App\Model\Orm\Entity\Azyl;
use App\Model\Orm\Entity\Messages;
use App\Model\Orm\Entity\Users;
use App\Model\Orm\Enums\MessageTypeEnum;
use App\Model\Orm\Enums\RoleTypeEnum;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\CityRepository;
use App\Model\Orm\Repository\MessagesRepository;
use App\Model\Orm\Repository\OwnersRepository;
use App\Model\Orm\Repository\UsersRepository;
use App\Components\Messenger\ChatControl;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Services\Menu;
use Contributte\Application\UI\BasePresenter;
use DateTimeImmutable;
use Nette;
use Nette\Application\UI\Form;
use App\Model\Orm\Entity\Owner;
use App\Services\MessagesService;
use Nette\Application\UI\InvalidLinkException;


class UserPresenter extends BasePresenter
{
    private roleFormFactory $roleFormFactory;
    private UsersRepository $usersRepository;
    private EntityManagerInterface $entityManager;
    private AzylRepository $azylRepository;
    private Users $currentUser; // Aktuálně přihlášený uživatel

    public function __construct(roleFormFactory                 $roleFormFactory,
                                UsersRepository                 $usersRepository,
                                AzylRepository                  $azylRepository,
                                EntityManagerInterface          $entityManager,
                        private readonly UserDetailsFormFactory $userDetailsFormFactory,
                        private readonly registerFormFactory    $registerFormFactory,
                        private readonly PhotoUploadFormFactory $photoUploadFormFactory,
                        private OwnersRepository                $ownerRepository,
                        private CityRepository                  $cityRepository,
                        private MessagesRepository              $messagesRepository,
                        private messagesFormFactory             $messagesFormFactory,
                        private messagesService                 $messagesService)
    {
        parent::__construct();
        $this->roleFormFactory = $roleFormFactory;
        $this->usersRepository = $usersRepository;
        $this->entityManager = $entityManager;
        $this->azylRepository = $azylRepository;
        $this->messagesService = $messagesService;

        $this->messagesFormFactory = $messagesFormFactory;
    }

    public function startup(): void
    {
        parent::startup();
        if (!$this->getPresenter()->getUser()->isLoggedIn())
        {
            $this->redirect('Home:signIn');

        }
        $menu = new Menu();
        $this->getTemplate()->messagesCount = $this->messagesRepository->countUnreadMessages($this->getPresenter()->getUser()->getId());
        $this->getTemplate()->mainMenuItems = $menu->getMenu();

    }
public function actionDefault(): void
    {
        if ($this->getPresenter()->getUser()->isLoggedIn())
        {
            if ($this->getPresenter()->getUser()->isInRole(RoleTypeEnum::ROLE_USER))
            {
                $this->getPresenter()->redirect('User:first');
            }
            elseif ($this->getPresenter()->getUser()->isInRole(RoleTypeEnum::ROLE_AZYL))
            {
                $this->getPresenter()->redirect('Azyl:profil');
            }
            else
            {
                $this->getPresenter()->redirect('User:profil');
            }
        }
        else
        {
            $this->redirect('Home:signIn');
        }
    }

    public function renderDefault(): void
    {

        $this->template->title = 'Admin';
    }

    public function renderAnimals(): void
    {
        $this->template->title = 'Animals';
    }

    public function renderAzyls(): void
    {
        $this->template->title = 'Azyls';
    }

    public function renderNews(): void
    {
        $this->template->title = 'News';
    }

    public function renderProfil(): void
    {
        $this->template->title = 'Profil';
    }

    public function actionMessages($id): void
    {
        $this->template->title = 'Zprávy';
        $messages =  $this->messagesService->getUserContacts($this->getUser()->getId());
            foreach($messages as $message)
                {
                    $chats[$message->getSenderAddress()] = $message->getSender()->getUsername();
                }
        $this->template->chats = $chats;
        $this->redrawControl('chats');
        $this->redrawControl('messagesCount');
        $this->redrawControl('messages');
    }

    public function handleChat(string $id): void
    {
        $messages = $this->messagesRepository->getMessagesBySenderReceiverAddress(senderAddress: $id, receiverAddress: $this->getPresenter()->getUser()->getIdentity()->getData()['User']->getMessageAddress());

        $this->getTemplate()->messages = $messages;
        $this->getTemplate()->receiver = $id;
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


    /**
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

    public function handleChat(string $address): void
    {
    $messages = $this->messagesRepository->getMessagesBySenderReceiverAddress(senderAddress: $address, receiverAddress: $this->getPresenter()->getUser()->getIdentity()->getData()['User']->getMessageAddress());
    $this->getTemplate()->messages = $messages;
    $this->getTemplate()->receiver = $address;

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
        $message = $this->messagesRepository->getMessagesById($id);
        if($message && $message->getReceiverAddress() === $this->getPresenter()->getUser()->getIdentity()->getData()['User']->getMessageAddress()) {

            $redirectId = $message->getSender()->getId();
            $message->setDeletedAt(new DateTimeImmutable());
            $this->messagesRepository->save($message);
            $this->flashMessage('Vzkaz byl smazán', 'info');
        } else {

            $this->flashMessage('Chyba mazání vzkazu', 'danger');
        }

        if($this->isAjax()){
            $this->redrawControl('messages');
        }
        else {
            $chat = "?do=chat";
            $url = $this->link('User:messages', $redirectId) . $chat;
            $this->redirectUrl($url);

        }

    }
 **/
    public function messagesFormSucceeded(Form $form, \stdClass $values) : void
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
            $url = $this->link('User:messages', $values->id) . $chat;
            $this->redirectUrl($url);

        }

    }
    public function handleSendMessage(): void
    {
        $this->getPresenter()->isAjax();
        $this->messagesService->messagesFormSucceeded();


        /*
        $this->getPresenter()->isAjax();
        $message = New Messages();
        $message->setSender($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
        $message->setType(MessageTypeEnum::FROMUSER_TYPE);
        $message->setReceiver($this->usersRepository->getUserById($values->id));
        $message->setMessage($values->message);
        $message->setCreatedAt(new DateTimeImmutable());
        $message->setReaded(false);
        $this->messagesRepository->save($message);
        $this->redrawControl('messages');
*/
    }

    public function renderAdoptions(): void
    {
        $this->template->title = 'Adoptions';
    }
    // Actions

    public function renderFirst()
    {
        $this->template->title = 'Vyberte si roli';

    }

    public function createComponentRoleForm(): Form
    {
        $form = $this->roleFormFactory->create();
        $form->onSuccess[] = [$this, 'roleFormSucceeded'];
        return $form;
    }

    public function userDetailsFormSucceeded(Form $form,  \stdClass $values) : void
    {
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());

        $user->setUpdatedAt(new DateTimeImmutable());
        $user->setUpdatedBy($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
        $user->setFirstName($values->firstName);
        $user->setLastName($values->lastName);

        $this->getPresenter()->flashMessage('Detaily byly úspěšně uloženy!', 'alert-success');
        $this->getPresenter()->redirect('User:profil');
    }

    /**
     * @throws InvalidLinkException
     */
    public function createComponentUserDetailForm(): Form
    {
        $form = $this->userDetailsFormFactory->create($this->getPresenter());
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());

        $form->setDefaults($user->toArray());

        $form['send']->setHtmlAttribute('class', 'btn btn-primary');
        $form['send']->setCaption('Uložit změny');

        $form->onSuccess[] = [$this, 'userUpdateFormSucceeded'];
        return $form;
    }

    public function createComponentUserUpdateForm(string $name): ?Nette\ComponentModel\IComponent
    {
       $form = $this->registerFormFactory->create();
       $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
       $form->setDefaults($user->toArray());
       return $form;
    }

    public function createComponentOwnerPhotoUploadForm(): Form
    {
        $form = $this->photoUploadFormFactory->create();
        $form->onSuccess[] = [$this, 'ownerPhotoUploadFormSucceeded'];
        return $form;
    }

    public function createComponentMessagesForm(): Form
    {
        $form = $this->messagesFormFactory->create();
        $form->onSuccess[] = [$this, 'messagesFormSucceeded'];
        return $form;
    }



    public function ownerPhotoUploadFormSucceeded(Form $form, \stdClass $values): void
    {
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
        $user->setPhotos($values->photos);
        $this->usersRepository->addUser($user);
        $this->getPresenter()->flashMessage('Fotky byly úspěšně nahrány!', 'alert-success');
        $this->presenter->redrawControl('photos');
    }

    //TODO: Tady se musí dodělat vazba na ownera a Azyl kde má každý specifické údaje a je potřeba to rozdělit po výběru


    public function roleFormSucceeded(Form $form, \stdClass $values): void
    {
        if ($values->role === RoleTypeEnum::ROLE_AZYL)
        {
            $azyl = new Azyl();

            $users = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
            $users->setRole(RoleTypeEnum::ROLE_AZYL);
            $users->setUpdatedAt(new DateTimeImmutable());
            $users->setUpdatedBy($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));


            $this->azylRepository->saveAzyl($azyl);
            $users->setAzyl($azyl->getId());
            $this->usersRepository->addUser($users);


            $this->getPresenter()->flashMessage('Od této chvíle jste v roli Azylu! Znovu se přihlašte!', 'alert-success');
            $this->getPresenter()->getUser()->logout();
            $this->redirect('Home:signIn');
        }
        elseif ($values->role === RoleTypeEnum::ROLE_OWNER)
        {

            $users = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
            $users->setRole(RoleTypeEnum::ROLE_OWNER);
            $users->setUpdatedAt(new DateTimeImmutable());
            $users->setUpdatedBy($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
            $owner = new Owner();
            $owner->setUser($users);
            $this->usersRepository->addUser($users);
            $this->getPresenter()->flashMessage('Od této chvíle jste běžný uživatel! Znovu se prosím přihlašte!', 'alert-success');
            $this->getPresenter()->getUser()->logout();
            $this->redirect('Home:signIn');

        }

    }
    public function createComponentChat(): ChatControl
    {
        return new ChatControl($this->entityManager, $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()) );
    }
}