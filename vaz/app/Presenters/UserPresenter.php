<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\PhotoUploadFormFactory;
use App\Forms\RegisterFormFactory;
use App\Forms\messagesFormFactory;
use App\Forms\roleFormFactory;
use App\Forms\userDetailsFormFactory;
use App\Model\Orm\Entity\Azyl;
use App\Model\Orm\Entity\Photo;
use App\Model\Orm\Entity\Users;
use App\Model\Orm\Enums\RoleTypeEnum;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\CityRepository;
use App\Model\Orm\Repository\MessagesRepository;
use App\Model\Orm\Repository\OwnersRepository;
use App\Model\Orm\Repository\PhotosRepository;
use App\Model\Orm\Repository\UsersRepository;
use App\Components\Messenger\ChatControl;
use App\Model\VersionService;
use App\Services\AnalyticsService;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\Services\Menu;
use Contributte\Application\UI\BasePresenter;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\NoReturn;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use Nepada\Bridges\PhoneNumberInputDI\PhoneNumberInputExtension;
use Nepada\PhoneNumberDoctrine\PhoneNumberType;
use Nepada\PhoneNumberInput\PhoneNumberInput;
use Nette;
use Nette\Application\UI\Form;
use App\Model\Orm\Entity\Owner;
use App\Services\MessagesService;
use Nette\Application\UI\InvalidLinkException;
use Nextras\Dbal\Platforms\MySqlPlatform;


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
                        private readonly registerFormFactory      $registerFormFactory,
                        private readonly PhotoUploadFormFactory   $photoUploadFormFactory,
                        private OwnersRepository                  $ownerRepository,
                        private readonly CityRepository           $cityRepository,
                        private readonly MessagesRepository       $messagesRepository,
                        private messagesFormFactory               $messagesFormFactory,
                        private messagesService                   $messagesService,
                        private analyticsService                  $analyticsService,
                        private photosRepository                  $photosRepository,
                        private readonly Nette\Security\Passwords $passwords,
                        private readonly VersionService           $versionService,)
    {
        parent::__construct();
        $this->roleFormFactory = $roleFormFactory;
        $this->usersRepository = $usersRepository;
        $this->entityManager = $entityManager;
        $this->azylRepository = $azylRepository;
        $this->analyticsService = $analyticsService;
        $this->messagesService = $messagesService;
        $this->messagesFormFactory = $messagesFormFactory;
        $this->photosRepository = $photosRepository;

    }

    public function startup(): void
    {
        parent::startup();
        if (!$this->getPresenter()->getUser()->isLoggedIn())
        {
            $this->redirect('Home:signIn');

        }
        if ($this->isAjax()) {
            $this->checkRequirements(null); // Zruší povinné přihlášení pro AJAX
        }

        $this->analyticsService->setPresenter($this);
        $this->analyticsService->setComment('User presenter |'.$this->getPresenter()->getAction().' | '.$this->getPresenter()->getUser()->getIdentity()->getId());
        $this->analyticsService->logVisit();

        
        $menu = new Menu();
        $this->getTemplate()->messagesCount = $this->messagesRepository->countUnreadMessages($this->getPresenter()->getUser()->getId());
        $this->getTemplate()->mainMenuItems = $menu->getMenu();

    }

    protected function beforeRender(): void
    {
        $this->template->addFilter('safeHtml', function (string $html): string {
            $allowedTags = ['b', 'i', 'a'];
            $html = strip_tags($html, '<' . implode('><', $allowedTags) . '>');

            // Povolit pouze bezpečné atributy v <a>
            return preg_replace_callback('/<a\s+([^>]+)>/i', function ($matches) {
                if (preg_match('/href=["\'](.*?)["\']/', $matches[1], $hrefMatch)) {
                    return '<a href="' . htmlspecialchars($hrefMatch[1], ENT_QUOTES) . '">';
                }
                return '<a>';
            }, $html);
        });

        $this->getTemplate()->version = $this->versionService->getLastVersion();
    }

#[NoReturn] public function actionDefault(): void
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
        $user = $this->usersRepository->getUserById($this->getUser()->getId());
        $this->getTemplate()->title = 'Uživatelský Profil';
        $this->getTemplate()->personalPhoto = $this->photosRepository->findById($user->getPersonalPhoto());
        $this->getTemplate()->adoptions = $user->getAdoptions();
        $this->getTemplate()->photos = $user->getPhotos();

        $city = $this->cityRepository->findOneBy(['id' => $user->getCity()]); //co to tady je
        if ($city !== null) {
            $this->getTemplate()->regions = $this->cityRepository->findRegionByCountry($city->getCountry());
            $this->getTemplate()->cities = $this->cityRepository->findCityByRegionArray($city->getRegion());

            $this->getTemplate()->city = $city->getId();
            $this->getTemplate()->region = $city->getRegion();
        }
        else
        {
            $this->getTemplate()->regions = $this->cityRepository->fetchCountries();
            $this->getTemplate()->cities = $this->cityRepository->findCityByRegionArray('Kroměříž');

            $this->getTemplate()->city = null;
            $this->getTemplate()->region = null;
        }
    }

    public function actionMessages($id): void
    {
        $chats = [];
        $this->getTemplate()->title = 'Zprávy';
        $messages =  $this->messagesService->getUserContacts($this->getUser()->getId());

            foreach($messages as $message)
                {
                   $chats[$message->getSenderAddress()] = $message->getSender()->getUsername();
                }
        $this->getTemplate()->chats = $chats;
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

    public function handleUpdateRegions(string $country): void
    {
        $this->getTemplate()->regions = $this->cityRepository->findRegionByCountry($country);
        $this->redrawControl('regionSelect');
    }

    public function handleUpdateCities(string $region): void
    {
        $this->getTemplate()->cities = $this->cityRepository->findCityByRegionArray($region);
        $this->redrawControl('citySelect');
    }

    public function handleGetRegions(string $country): void
    {
        $this->getTemplate()->regions = $this->cityRepository->findRegionByCountry($country);
        $this->redrawControl('regionSelect');
    }

    public function handleGetCities(string $region): void
    {
        $this->getTemplate()->cities = $this->cityRepository->findCityByRegionArray($region);
        $this->redrawControl('citySelect');
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

    }
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
    /*
    #[NoReturn] public function userDetailsFormSucceeded(Form $form, \stdClass $values) : void
    {
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());

        $user->setUpdatedAt(new DateTimeImmutable());
        $user->setUpdatedBy($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
        $user->setFirstName($values->firstName);
        $user->setLastName($values->lastName);

        $this->getPresenter()->flashMessage('Detaily byly úspěšně uloženy!', 'alert-success');
        $this->getPresenter()->redirect('User:profil');
    }
        */
    /**
     * @throws InvalidLinkException
     * @throws NumberParseException
     */
    public function createComponentUserDetailsForm(): Form
    {
        $form = $this->userDetailsFormFactory->create($this->getPresenter());
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
        bdump($user);
        if (!is_null($user->getCity()))
        {
            $test = $form->components;
            $city = $this->cityRepository->findOneBy(['id'=>$user->getCity()]);
            if (!is_null($city)) {
                $form->removeComponent($form->getComponent('city'));
                $form->removeComponent($form->getComponent('region'));
                $form->removeComponent($form->getComponent('country'));

                $form->addSelect('country', 'Země', $this->cityRepository->fetchCountries());
                $form->addSelect('region', 'Region', $this->cityRepository->findRegionByCountry($city->getCountry()));
                $form->addSelect('city', 'Město', $this->cityRepository->findCityByRegionArray($city->getRegion()));
                $form->getComponent('region')->setItems($this->cityRepository->findRegionByCountry($city->getCountry()));
                $form->getComponent('city')->setItems($this->cityRepository->findCityByRegionArray($city->getRegion()));


                $form->setDefaults(['firstName' => $user->getFirstName(),
                                    'lastName' => $user->getLastName(),
                                    'phone' => $user->getPhone(),
                                    'street' => $user->getStreet(),
                                    'city' => is_null($user->getCity()) ? null : $city->getId(),
                                    'country' => is_null($user->getCity()) ? null : $city->getCountry(),
                                    'region' => is_null($user->getCity()) ? null : $city->getRegion(),
                                    'orientation' => $user->getOrientationNumber(),
                                    'house' => $user->getHouseNumber(),
                                    'description' => $user->getDescription()
                    ]);
            }

            $form->setDefaults(['firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'phone' => $user->getPhone(),
                'street' => $user->getStreet(),

                'orientation' => $user->getOrientationNumber(),
                'house' => $user->getHouseNumber(),
                'description' => $user->getDescription()
            ]);

        }

        $form['send']->setHtmlAttribute('class', 'btn btn-primary');
        $form['send']->setCaption('Uložit změny');

        $form->onSuccess[] = [$this, 'userDetailsFormSucceeded'];
        return $form;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NumberParseException
     * @throws ConversionException
     * @throws PhoneNumberParseException
     */
    public function userDetailsFormSucceeded(Form $form, \stdClass $values) : void
    {

        $post = $this->getPresenter()->getHttpRequest()->getPost();
        $user = $this->usersRepository->getUserById($this->getUser()->getId());

        if (!is_null($user))
            {
                $phoneNumber = \Brick\PhoneNumber\PhoneNumber::parse($post['phone']);

                $phone = $phoneNumber->format(PhoneNumberFormat::INTERNATIONAL);
                $user->setFirstName($post['firstName']);
                $user->setLastName($post['lastName']);
                $user->setUpdatedAt(new DateTimeImmutable());
                $user->setUpdatedBy($this->usersRepository->getUserById($this->getPresenter()->getUser()->getId()));
                $user->setPhone(phone: empty($post['phone']) ? null : $phone);
                $user->setOrientationNumber($post['orientation']);
                $user->setStreet($values->street);
                $user->setDescription($values->description);
                $user->setHouseNumber($post['house']);
                $user->setCity(intval($post['city']));
               // $user->setCity($this->cityRepository->findCityById(intval($post['city'])));
                $this->usersRepository->save($user);
                $this->flashMessage('Uživatelské informace aktualizovány.', 'alert-success');

            }

    }


    public function createComponentUserUpdateForm(string $name): ?Nette\ComponentModel\IComponent
    {
       $form = $this->registerFormFactory->create();
       $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
       $form->setDefaults($user->toArray());
       $form->addUpload('personalPhoto','Profilová fotka');
       $pass = $form->getComponent('password');
       $pass->setRequired(false);
       $pass2 = $form->getComponent('password2');
       $pass2->setRequired(false);
       $form->removeComponent($form->getComponent('phone'));
        $form->removeComponent($form->getComponent('legalTerms'));
        $form->removeComponent($form->getComponent('send'));
        $form->removeComponent($form->getComponent('adoptionVerification'));
       $form->addSubmit('aktualization','Aktualizovat');
       $form->onSuccess[] = [$this, 'userUpdateFormSucceeded'];

       return $form;
    }

    #[NoReturn] public function userUpdateFormSucceeded(Form $form, \stdClass $values) : void
    {

       $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
       $user->setUpdatedAt(new DateTimeImmutable('now'));
       $user->setUpdatedBy($user);

       if($user->getEmail() !== $values->email) {

           if ($this->usersRepository->findBy(['email' => $values->email]))
           {

               $this->flashMessage('Email už je v systému nebyl aktualizován!', 'alert-success');
           }
           else
           {
               $user->setEmail($values->email);
               $this->flashMessage('Email byl aktualizován. <b>POZOR!</b> email se používá pro přihlašovíní!! Email byl nastaven na: '.$values->email.'.', 'alert-success');
           }
       }

       if (!empty($values->password))
       {
           if($values->password == $values->password2)
           {
               $user->setPassword($this->passwords->hash($values->password));
               $this->flashMessage('POZOR! Heslo bylo aktualizováno!', 'alert-success');
           }
           else
           {
               $this->flashMessage('POZOR! Problém při aktualizaci hesla!', 'alert-danger');
           }
       }

       if (!empty($this->getRequest()->files['personalPhoto']))
       {
           $photo = new Photo();
           $photo->setUser($user);
           $photo->setDate(new DateTimeImmutable('now'));
           $photo->uploadUserPersonalPhoto($values->personalPhoto);
           $this->photosRepository->save($photo);
           $user->setPersonalPhoto($photo->getId());
           $this->flashMessage('Osobní fotka nastavena!', 'alert-success');
       }
        $this->usersRepository->save($user);
        $this->flashMessage('Nastavení uživatele aktualizováno', 'alert-success');
        $this->redirect('this');
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
        foreach ($values->photos as $photo)
        {

            $photoUpload = New Photo();
            $photoUpload->setUser($user);
            $photoUpload->setDate(new DateTimeImmutable('now'));
            $photoUpload->uploadUserPhoto($photo);
            $this->photosRepository->save($photoUpload);
        }
        $this->usersRepository->addUser($user);
        $this->getPresenter()->flashMessage('Fotky byly úspěšně nahrány!', 'alert-success');
        $this->getPresenter()->redrawControl('photos');
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