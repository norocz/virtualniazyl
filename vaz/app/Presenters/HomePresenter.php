<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\adoptionFormFactory;
use App\Forms\registerFormFactory;
use App\Forms\SignInFormFactory;
use App\Model\Orm\Entity\Adoption;
use App\Model\Orm\Entity\AdoptionAction;
use App\Model\Orm\Entity\Azyl;
use App\Model\Orm\Entity\Messages;
use App\Model\Orm\Entity\Users;
use App\Model\Orm\Enums\ActionTypeEnum;
use App\Model\Orm\Enums\MessageTypeEnum;
use App\Model\Orm\Repository\AdoptionsRepository;
use App\Model\Orm\Repository\AnimalsRepository;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\MessagesRepository;
use App\Model\Orm\Repository\NewsRepository;
use App\Model\Orm\Repository\PhotosRepository;
use App\Model\Orm\Repository\UsersRepository;
use App\Services\AdoptionKeyService;
use App\Services\LogingService;
use App\Services\UserAddressService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Nette;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\Forms\Form;
use Nette\Utils\DateTime;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use App\Model\Services\Menu;
use Defr\QRPlatba\QRPlatba;

final class HomePresenter extends Nette\Application\UI\Presenter
{
    protected EntityManagerInterface $entityManager;
    protected UsersRepository $usersRepository;

    public Azyl $azylProfil;
    public Users $azylUser;



    public function __construct(UsersRepository                        $usersRepository,
                                EntityManagerInterface                 $entityManager,
                                protected readonly SignInFormFactory   $signInFormFactory,
                                protected readonly RegisterFormFactory $registerFormFactory,
                                private readonly   Passwords             $passwords,
                                public             TemplateFactory      $templateFactory,
                                public readonly    NewsRepository         $newsRepository,
                                public readonly    AzylRepository         $azylRepository,
                                public             AdoptionsRepository    $adoptionsRepository,
                                public             PhotosRepository    $photosRepository,
                                private MessagesRepository             $messagesRepository,
                                private UserAddressService            $userAddressService,
                                private QRPlatba                    $QRPlatba,
                                private AnimalsRepository           $animalsRepository,
                                private adoptionFormFactory         $adoptionFormFactory,
                                private adoptionAction              $adoptionAction,
                                private logingService               $logingService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->usersRepository = $usersRepository;
        $this->QRPlatba = $QRPlatba;
        $this->messagesRepository = $messagesRepository;
        $this->userAddressService  = $userAddressService;
        $this->animalsRepository = $animalsRepository;
        $this->adoptionFormFactory = $adoptionFormFactory;
        $this->adoptionAction = $adoptionAction;
        $this->logingService = $logingService;

    }

    public function startup(): void
    {
        parent::startup();
        $menu = new Menu();

        if ($this->getPresenter()->getUser()->isLoggedIn())
        {
            $this->getTemplate()->messagesCount = $this->messagesRepository->countUnreadMessages($this->getPresenter()->getUser()->getId());

        }
        $this->getTemplate()->mainMenuItems = $menu->getMenu();
        //$this->getTemplate()->userRepository = $this->usersRepository;
    }

    public function renderDefault(): void
    {

        $news = $this->newsRepository->findVisibleNews();
        $adoptions = $this->animalsRepository->findBy(['toAdoption' => true, 'isDeleted' => false],  ['id' => 'DESC'],8);

        $this->getTemplate()->title = 'Domácí stránka';
        $this->getTemplate()->adoptions = $adoptions;
        $this->getTemplate()->news = $news;
        $this->getTemplate()->newsCount = $this->newsRepository->count(['deleted' => false, 'global' => true]);



    }

    public function renderNews($offset = 0): void
    {
        $news = $this->newsRepository->findBy(['deleted' => false, 'global' => true],  ['createdAt' => 'DESC'],20, $offset);
        $this->getTemplate()->title = 'Všechny Novinky';
        $this->getTemplate()->news = $news;
        $this->getTemplate()->newsCount = $this->newsRepository->count(['deleted' => false, 'global' => true]);
        $this->getTemplate()->offset = $offset;
    }

    public function renderAzyls(): void
    {

        $this->getTemplate()->title = 'Všechny azyl';
        $this->getTemplate()->azyls = $this->azylRepository->fetchLast();
    }

    public function renderAdoptions($offset = 0): void
    {
        $this->getTemplate()->title = 'Všechny adopce';
        $this->getTemplate()->adoptions = $this->animalsRepository->findBy(['isDeleted' => false, 'toAdoption' => true],  ['id' => 'DESC'], 20, $offset);
    }

    public function renderAdopce(int $id): void
    {
        $adopce = $this->animalsRepository->findById(intval($id));
        $this->getTemplate()->title = 'Adopce - ' . $adopce->getName();
        $this->getTemplate()->adopce = $adopce;

        if ($this->getUser()->isLoggedIn())
        {
            $aks = new AdoptionKeyService();
            $aks->createKey($this->getUser()->getId(), $adopce->getId(), $adopce->getAzyl()->getId());
            $adoptionKey = $aks->getKey();
            $adoptions = $adopce->getAdoption();
            $test = $this->adoptionsRepository->findOneBy(['adoptionKey' => $adoptionKey]);


                    if ($test) {
                        $this->getTemplate()->status = true;
                    } else {
                        $this->getTemplate()->status = false;
                    }


        } else {

            $this->flashMessage('Pro adoptování je potřeba se přihlásit!','alert-danger');
           // $this->redirect('Home:SignIn');
        }
    }
    public function renderAzyl(int $id) : void
    {
        $qrPlatba =New QRPlatba();

        $azylProfil = $this->azylRepository->findById($id);
        $now = new DateTimeImmutable();
        $azylNews = $azylProfil->getAzylNews();
        $azylUser = $this->usersRepository->getUserByAzylId($id);
        $this->getTemplate()->azylProfil = $azylProfil->toArray();
        $this->getTemplate()->azylNews = $azylNews;
        $this->getTemplate()->azylUser = $azylUser;
        $this->getTemplate()->title = 'Azyl -' . $azylProfil->getAzylName();
        $this->getTemplate()->adoptions = $this->animalsRepository->findBy(['azyl' => $azylProfil, 'toAdoption' => true], ['id' => 'DESC']);

        if (!is_null($azylProfil->getBankAccount())){
        $qrPlatba->setAccount($azylProfil->getBankAccount().'/'.$azylProfil->getBankCode())
                    ->setMessage('Peníze pro '.$azylProfil->getAzylName())
                    ->setVariableSymbol($azylProfil->getBankSpecificCode())
                    ->setCurrency('CZK')
                    ->setAmount((float)'101.11')
                    ->setDueDate(new \DateTime('now'));
            }

        $this->getTemplate()->qrkodazyl = $qrPlatba->getQRCodeImage();
    }

    public function actionAzylAdoptions(int $id) : void
    {
        $azylProfil = $this->azylRepository->findById($id);

        $azylUser = $this->usersRepository->getUserByAzylId($id);
        $this->getTemplate()->azylProfil = $azylProfil->toArray();
        $this->getTemplate()->azylAdoptions = $this->adoptionsRepository->findBy(['id' => $azylProfil->getId()], ['createdAt' => 'DESC']);
        $this->getTemplate()->azylUser = $azylUser;
        $this->getTemplate()->title = 'Azyl -' . $azylProfil->getAzylName();
        $this->getTemplate()->newsCount = $this->newsRepository->count(['deleted' => false, 'author' => $azylUser->getId()]);

    }

    public function actionAzylNews(int $id) : void
    {
        $azylProfil = $this->azylRepository->findById($id);

        $azylUser = $this->usersRepository->getUserByAzylId($id);
        $this->getTemplate()->azylProfil = $azylProfil->toArray();
        $this->getTemplate()->azylNews = $this->newsRepository->findBy(['author'=> $azylUser->id], ['createdAt' => 'DESC']);
        $this->getTemplate()->azylUser = $azylUser;
        $this->getTemplate()->title = 'Azyl -' . $azylProfil->getAzylName();
        $this->getTemplate()->newsCount = $this->newsRepository->count(['deleted' => false, 'author' => $azylUser->getId()]);

    }

    public function actionAzylPhotos(int $id) : void
    {
        $azylProfil = $this->azylRepository->findById($id);

        $azylUser = $this->usersRepository->getUserByAzylId($id);
        $this->getTemplate()->azylProfil = $azylProfil->toArray();
        $this->getTemplate()->azylPhotos = $this->photosRepository->fetchByAzylId($id);
        $this->getTemplate()->azylUser = $azylUser;
        $this->getTemplate()->title = 'Azyl -' . $azylProfil->getAzylName();
        $this->getTemplate()->newsCount = $this->newsRepository->count(['deleted' => false, 'author' => $azylUser->getId()]);

    }

    public function actionSignIn(): void
    {
        $this->getTemplate()->title = 'Přihlášení';

    }

    public function actionRegistration()
    {
        $this->getTemplate()->title = 'Registrace';
        $this->getTemplate()->kytka = 'kytka'.rand(1,4).'.jpeg';

    }
    public function actionRegistered(): void
    {
        $this->getTemplate()->title = 'Registrace proběhla v pořádku';
        $this->getTemplate()->kytka = 'kytka'.rand(1,4).'.jpeg';
        $vrf = $this->getPresenter()->getParameter('vrf');
        if (!empty($vrf))
        {
            $user = $this->usersRepository->getUserByMailVerifyToken($vrf);
            if($user !== NULL)
            {
                $user->setMailverified(TRUE);
                $user->setMailVerifyToken(NULL);
                $user->setMessageAddress($this->userAddressService->generateCommunicationAddress($user->getId(), $user->getEmail(), $user->getUserName()));
                $this->usersRepository->addUser($user);
                $this->getPresenter()->flashMessage('Váš email byl ověřen. Můžete se přihlásit.', 'alert-success');
                $this->getPresenter()->redirect('Home:signIn');
            }
            else
            {
                $this->getPresenter()->flashMessage('Ověření emailu se nezdařilo. Zkuste to prosím znovu.', 'alert-warning');
                $this->getPresenter()->redirect('Home:registered');
            }
        }
    }

    public function actionLogedIn(): void
    {
        $this->getTemplate()->title = 'Přihlášení';
    }

    public function actionThanks(): void
    {
        $this->getTemplate()->title = 'Poděkování autorů';
    }

    public function actionAzyl($id): void
    {
        $id = $this->getPresenter()->getParameter('id');
        $this->getTemplate()->title = 'Azyl';
        $this->getTemplate()->azyl = $this->azylRepository->getAzyl($id);
    }

    public function actionLogOut(): void
    {
        $this->getUser()->logout();
        $this->getPresenter()->flashMessage('Odhlášení proběhlo v pořádku.', 'alert-success');
        $this->redirect('Home:default');
    }

    public function createComponentSignInForm(): Form
    {

        $passwords = new Nette\Security\Passwords;
        $form = (new SignInFormFactory())->create();
        $form->onSuccess[] = [$this, 'formSignInSucceeded'];

        return $form;
    }
    public function formSignInSucceeded(\Nette\Application\UI\Form $form, \stdClass $values): void
    {
        try {
            $this->getUser()->login($values->email, $values->password);
            $this->getPresenter()->flashMessage('Přihlášení se zdařilo', 'alert-success');
            if ($this->getUser()->isInRole('user')) {
                $this->getPresenter()->redirect('User:first');
            }
            $message = new Messages();
            $admin = $this->usersRepository->getUserById(1);
            $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
            $message->setSender($this->usersRepository->getUserByMessageAddress('sys'));
            $message->setSenderAddress('sys');
            $message->setCreatedAt(new DateTimeImmutable());
            $message->setMessage('Přihlásil se uživatel:'.$user->getUserName());
            $message->setReceiver($admin);
            $message->setReceiverAddress($admin->getMessageAddress());
            $message->setType(MessageTypeEnum::TOADMIN_TYPE);
            $message->setReaded(false);
            $this->messagesRepository->save($message);
            if ($values->remember) {
                $this->user->setExpiration('14 days'); // Uživatel zůstane přihlášen 14 dní
            } else {
                $this->user->setExpiration('20 minutes', true); // Standardní 20 minutová expirace
            }

            $this->getPresenter()->redirect('Home:default');
        } catch (AuthenticationException $e) {
            $this->getPresenter()->flashMessage('Email nebo heslo jsou špatně', 'alert-warning');

        }
    }

    public function createComponentRegisterForm(): Form
    {
        $form = (new registerFormFactory($this->usersRepository, $this->entityManager))->create();
        $form->onSuccess[] = [$this, 'formRegisterSucceeded'];
        return $form;
    }

    public function createComponentAdoptionForm(): Form
    {
        $form = (new AdoptionFormFactory())->create();
        $form->onSuccess[] = [$this, 'formAdoptionSucceeded'];
        return $form;
    }

    #[NoReturn] public function formAdoptionSucceeded(Form $form, \stdClass $values): void
    {
       $animal = $this->animalsRepository->findById(intval($this->getPresenter()->getParameter('id')));

       $aks = new AdoptionKeyService();
       $aks -> createKey($this->getUser()->id, $animal->getId(),$animal->getAzyl()->getId());
       $key =  $aks->getKey();
       bdump($key);
       $adoption  = new Adoption();
       $adoption -> setDescription($values->description);
       $adoption -> setAnimal($animal);
       $adoption -> setAdoptionKey($key);
       $adoption -> setCreatedAt(new DateTimeImmutable());
       $adoption -> setUpdatedAt(new DateTimeImmutable());
       $adoption -> setAdoptionType($animal->getAdoptionType());
       $adoption -> setActionType(ActionTypeEnum::START_ADOPTION);
       $adoption -> setUser($this->usersRepository->getUserById($this->getPresenter()->getUser()->id));
       $adoption -> setAzyl($animal->getAzyl());
       $adoption -> setSetings('adopce');
       $adoption -> setDeleted(false);
       $adoption -> setConfirmed(false);
       $adoption -> setCanceled(false);
       $adoption -> setActionType(ActionTypeEnum::START_ADOPTION);
       $adoption -> setAdoptionType($animal->getAdoptionType());


       $this->adoptionsRepository->saveAdoption($adoption);

       $this->getPresenter()->flashMessage('Žádost o adopci byla odeslána!', 'alert-success');
       $this->getPresenter()->redirect('this');


    }

    public function formRegisterSucceeded(Form $form, \stdClass $values):void
    {
        if(!($values->username === $this->usersRepository->getUserByUserName($values->username) || $values->email === $this->usersRepository->getUserByEmail($values->email) || $values->password === $values->password2))
        {
            $form->addError('Hesla nejsou stejná, nebo některý z údajů je již registrován!');
        }
        else {
            try {

                $now = new DateTimeImmutable();
                $token = md5($values->email.$now->format('Y-m-d H:i:s'));

                $user = new Users();
                $user->setUserName(strval($values->username));
                $user->setEmail(strval($values->email));
                $user->setPassword($this->passwords->hash($values->password));
                $user->setRole('user');
                $user->setCreatedAt($now);
                $user->setVerified(FALSE);
                $user->setPhone($values->phone);
                $user->setLegalTerms($values->legalTerms);
                $user->setAdoptionVerification($values->adoptionVerification);
                $user->setMailverified(FALSE);
                $user->setDeleted(FALSE);
                $user->setBaned(FALSE);
                $user->setPhoneVerified(FALSE);
                $user->setMailVerifyToken($token);
                $this->usersRepository->addUser($user);

                //Send registration email


                $verificationlink = $this->link('//Home:registered', ['vrf' => $token]);


                $template = $this->templateFactory->createTemplate();
                $html = $template->renderToString(__DIR__ . '/Template/Email/RegistrationEmail.latte', ['verificationLink' => $verificationlink]);

                $mail = new Message;
                $mail->setFrom('Registrace Virtuální Azyl <registration@virtualniazyl.cz>')
                    ->addTo(strval($values->email))
                    ->setSubject('Registrace na Virtuální Azyl')
                    ->setHtmlBody($html);

                //Mail sending
                $mailer = new SmtpMailer(
                    host: 'smtp.seznam.cz',
                    username: 'registration@virtualniazyl.cz',
                    password: "ing('stri55+",
                    port: 465,
                    encryption: 'ssl',
                    timeout: 600
                );

                $mailer->send($mail);

                $this->getPresenter()->flashMessage('Registrace proběhla v pořádku :-)', 'alert-success');
                $this->getPresenter()->redirect('Home:Registered');
            } catch (AuthenticationException $e) {
                $form->addError('Registrace se nezdařila možná jméno nebo email jsou již registrovány');
            }
        }

    }

}
