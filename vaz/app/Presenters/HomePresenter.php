<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\adoptionFormFactory;
use App\Forms\azylSendMessageFormFactory;
use App\Forms\contractSignFormFactory;
use App\Forms\paymentFormFactory;
use App\Forms\registerFormFactory;
use App\Forms\searchFormFactory;
use App\Forms\SignInFormFactory;
use App\Model\Orm\Entity\Adoption;
use App\Model\Orm\Entity\AdoptionAction;
use App\Model\Orm\Entity\Azyl;
use App\Model\Orm\Entity\Conversations;
use App\Model\Orm\Entity\Messages;
use App\Model\Orm\Entity\Payments;
use App\Model\Orm\Entity\Users;
use App\Model\Orm\Enums\ActionTypeEnum;
use App\Model\Orm\Enums\MessageTypeEnum;
use App\Model\Orm\Enums\PaymentStatusEnum;
use App\Model\Orm\Repository\AdoptionsRepository;
use App\Model\Orm\Repository\AnimalsRepository;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\CollectionsRepository;
use App\Model\Orm\Repository\ConversationsRepository;
use App\Model\Orm\Repository\FirewallLogsRepository;
use App\Model\Orm\Repository\MessagesRepository;
use App\Model\Orm\Repository\NewsRepository;
use App\Model\Orm\Repository\PaymentsRepository;
use App\Model\Orm\Repository\PhotosRepository;
use App\Model\Orm\Repository\UsersRepository;
use App\Model\Service\Firewall;
use App\Model\VersionService;
use App\Services\AdoptionKeyService;
use App\Services\AnalyticsService;
use App\Services\LogingService;
use App\Services\UserAddressService;
use DateTimeImmutable;
use Defr\QRPlatba\QRPlatbaException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use JetBrains\PhpStorm\NoReturn;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Nette;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\Forms\Form;
use App\Services\EmailService;
use Nette\Security\AuthenticationException;
use Nette\Security\Passwords;
use App\Model\Services\Menu;
use Defr\QRPlatba\QRPlatba;
use Nette\SmartObject;

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
                                private logingService               $logingService,
                                private emailService                $emailService,
                                private AnalyticsService         $analyticsService,
                                private Firewall                    $firewall,
                                private CollectionsRepository      $collectionsRepository,
                                private paymentsRepository          $paymentsRepository,
                                private readonly VersionService              $versionService,
                                private readonly azylSendMessageFormFactory $azylSendMessageFormFactory,
                                private readonly searchFormFactory         $searchFormFactory,
                                private  conversationsRepository $conversationsRepository,
                                private contractSignFormFactory        $contractSignFormFactory,
                                private FirewallLogsRepository          $firewallLogsRepository,)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->usersRepository = $usersRepository;
        $this->QRPlatba = $QRPlatba;
        $this->emailService = $emailService;
        $this->messagesRepository = $messagesRepository;
        $this->userAddressService  = $userAddressService;
        $this->animalsRepository = $animalsRepository;
        $this->adoptionFormFactory = $adoptionFormFactory;
        $this->adoptionAction = $adoptionAction;
        $this->logingService = $logingService;
        $this->analyticsService = $analyticsService;
        $this->firewall = $firewall;
        $this->firewall->setPresenter($this->getPresenter());
        $this->collectionsRepository = $collectionsRepository;
        $this->paymentsRepository = $paymentsRepository;



    }

    public function startup(): void
    {
        parent::startup();
        $menu = new Menu();

        if ($this->getPresenter()->getUser()->isLoggedIn())
        {
            $this->getTemplate()->messagesCount = '' ;//$this->messagesRepository->countUnreadMessages($this->getPresenter()->getUser()->getId());

        }
        $this->getTemplate()->mainMenuItems = $menu->getMenu();
        //$this->getTemplate()->userRepository = $this->usersRepository;
        $this->analyticsService->setPresenter($this);
        $this->analyticsService->setComment('Home presenter |'.$this->getPresenter()->getAction().' | ');
        $this->analyticsService->logVisit();

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

    public function renderDefault(): void
    {


        $news = $this->newsRepository->findVisibleNews();
        $adoptions = $this->animalsRepository->findBy(['toAdoption' => true, 'isDeleted' => false],  ['id' => 'DESC'],8);

        $this->getTemplate()->title = 'Domácí stránka';
        $this->getTemplate()->adoptions = $adoptions;
        $this->getTemplate()->pined = $this->newsRepository->findOnePined();
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

    public function renderCollections(): void
    {
        $this->getTemplate()->title = 'Aktuálně běžící sbírky';
        $this->getTemplate()->collections = $this->collectionsRepository->fetchAllActive();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function renderCollection(int $key): void
    {
        $this->getTemplate()->title = 'Sbírka pro';
        $this->getTemplate()->collection = $this->collectionsRepository->findOneByKey($key);

        $kolik = $this->paymentsRepository->getTotalPayByCollectionKey($key);


        $this->getTemplate()->collectionPayments = intval($this->paymentsRepository->getTotalPayByCollectionKey($key));
        //$this->getTemplate()->collectionPayments = 104000;

    }

public function renderAdoptions($offset = 0): void
    {
        $placeholders = [
        "Hledat kamaráda",
        "Hledat myšku",
        "Najít parťáka na život",
        "Objevit přítulnou kobru",
        "Najít chlupatého šéfa",
        "Získat věrného ochránce",
        "Vybrat si gaučového experta",
        "Najít kočičího filozofa",
        "Najít psa s názorem",
        "Objevit zvířecí duši k adopci",
        "Hledat nejlepšího kamaráda",
        "Najít štěkacího rádce",
        "Najít spacího mistra",
        "Objevit roztomilý chaos",
        "Hledat kočičího krále",
        "Najít vrnící topení",
        "Vybrat si nového šéfa domácnosti",
        "Najít chundelatý poklad",
        "Hledat někoho, kdo sní tvoji svačinu",
        "Najít mistra v dělání nepořádku"];

        $this->getTemplate()->placeholders = $placeholders[array_rand($placeholders)];
        $this->getTemplate()->title = 'Všechny adopce';
        $this->getTemplate()->adoptions = $this->animalsRepository->findBy(['isDeleted' => false, 'toAdoption' => true],  ['id' => 'DESC'], 20, $offset);

    }

    public function renderAdopce(int $id): void
    {
        $name ='';
        $adopce = $this->animalsRepository->findById(intval($id));
        //$adopce = $this->adoptionsRepository->findOneBy(['id' => intval($id)]);

        $this->getTemplate()->title = 'Adopce ';
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
                        $this->getTemplate()->adopt = $test;
                    } else {
                        $this->getTemplate()->status = false;
                    }


        } else {

            $this->flashMessage('Pro adoptování je potřeba se přihlásit!','alert-danger');
           // $this->redirect('Home:SignIn');
        }
    }
    public function renderAzyl(int $id): void
    {
        $azylProfil = $this->azylRepository->findById($id);
        $userId = $this->getUser()->getId();



        if ($userId !== null) {
            $user = $this->usersRepository->getUserById($userId);
            $conversations = $this->conversationsRepository->findByUserAndAzyl($user, $azylProfil);

            if (count($conversations) > 1) {
                $lastConversation = $conversations[0];

                // Začneme transakci, aby vše probíhalo atomicky
                $this->entityManager->beginTransaction();

                try {
                    // Procházení všech konverzací a přesunutí zpráv
                    foreach ($conversations as $conversation) {
                        // Načteme všechny zprávy této konverzace
                        $messagesToMove = $this->messagesRepository->findBytConversationMessages($conversation->getId());
                        if (!is_null($conversation->getAdoption()))
                        {
                            break;
                        }

                        // Přesuneme všechny zprávy pod novou konverzaci
                        foreach ($messagesToMove as $messageToMove) {
                            $messageToMove->setConversation($lastConversation);  // Nastavíme novou konverzaci
                            $this->messagesRepository->save($messageToMove);    // Uložíme zprávu
                        }
                    }

                    // Uložíme všechny změny v zprávách
                    $this->entityManager->flush();

                    // Smazání starých konverzací
                    foreach ($conversations as $conversation) {
                        if (!is_null($conversation->getAdoption()))
                        {
                            break;
                        }
                        $this->conversationsRepository->remove($conversation);
                    }

                    // Uložíme změny a commitujeme transakci
                    $this->conversationsRepository->flush();
                    $this->entityManager->commit();

                    // Flash message, že konverzace byly spojeny
                    $this->flashMessage('Konverzace byly spojeny do jedné', 'alert-success');
                } catch (\Exception $e) {
                    // Pokud dojde k chybě, rollback transakce
                    $this->entityManager->rollback();
                    throw $e;  // Nebo můžeš logovat chybu
                }
            }

            // Když není více než jedna konverzace, použije se první nebo nová konverzace
            $conversation = empty($conversations) ? new Conversations() : $conversations[0];
            $conversation->setComment($this->getPresenter()->getAction().'|'.$this->getPresenter()->getName().'|'.$this->getUser()->getId());
            $conversation->setBlock(false);
            $conversation->setAzyl($azylProfil);
            $conversation->setUser($user);

            // Uložíme nebo aktualizujeme konverzaci
            $this->conversationsRepository->save($conversation);
            $this->getTemplate()->conversation = $conversation->getId();
        }

        $azylNews = $azylProfil->getAzylNews();
        $azylUser = $this->usersRepository->getUserByAzylId($id);

        // Předáme data do šablony
        $this->getTemplate()->azylProfil = $azylProfil;
        $this->getTemplate()->azylNews = $azylNews;
        $this->getTemplate()->azylUser = $azylUser;
        $this->getTemplate()->title = 'Azyl -' . $azylProfil->getAzylName();
        $this->getTemplate()->adoptions = $this->animalsRepository->findBy(['azyl' => $azylProfil, 'toAdoption' => true], ['id' => 'DESC']);
    }


    public function actionAzylAdoptions(int $id) : void
    {
        $azylProfil = $this->azylRepository->findById($id);

        $azylUser = $this->usersRepository->getUserByAzylId($id);
        $this->getTemplate()->azylProfil = $azylProfil;
        $this->getTemplate()->azylUser = $azylUser;
        $this->getTemplate()->title = 'Azyl -' . $azylProfil->getAzylName();
        $this->getTemplate()->newsCount = $this->newsRepository->count(['deleted' => false, 'author' => $azylUser->getId()]);

    }

    public function actionAzylNews(int $id) : void
    {
        $azylProfil = $this->azylRepository->findById($id);

        $azylUser = $this->usersRepository->getUserByAzylId($id);
        $this->getTemplate()->azylProfil = $azylProfil;
      $this->getTemplate()->azylNews = $azylProfil->getAzylNews();   // $this->newsRepository->findBy(['author'=> $azylUser->getId()], ['createdAt' => 'DESC']);
        $this->getTemplate()->azylUser = $azylUser;
        $this->getTemplate()->title = 'Azyl -' . $azylProfil->getAzylName();
        $this->getTemplate()->newsCount = $this->newsRepository->count(['deleted' => false, 'author' => $azylUser->getId()]);

    }

    public function actionAzylPhotos(int $id) : void
    {
        $azylProfil = $this->azylRepository->findById($id);

        $azylUser = $this->usersRepository->getUserByAzylId($id);
        $this->getTemplate()->azylProfil = $azylProfil;
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
    public function formSignInSucceeded(Form $form, \stdClass $values): void
    {
        if ($this->firewall->isUserTemporarilyBlocked()) {
            $this->flashMessage('Počkejte 30 sekund před dalším pokusem.', 'alert-warning');
            $this->redirect('this');
            }

        if ($this->firewall->isUserPermanentlyBlocked()) {
            $this->flashMessage('Přihlášení bylo zablokováno. Napište adminům na admin@virtualniazyl.cz', 'alert-warning');
            $this->redirect('Home:default');
        }

        try {
            $this->getUser()->login($values->email, $values->password);
            $this->firewall->unBlockUser();
            $this->getPresenter()->flashMessage('Přihlášení se zdařilo', 'alert-success');

            if ($this->getUser()->isInRole('user')) {
                $this->getPresenter()->redirect('User:first');
            }

            if ($values->remember) {
                $this->user->setExpiration('14 days'); // Uživatel zůstane přihlášen 14 dní
            } else {
                $this->user->setExpiration('20 minutes', true); // Standardní 20 minutová expirace
            }

            $this->getPresenter()->redirect('Home:default');
        } catch (AuthenticationException $e) {
            $this->firewall->logFailedLogin(); // Logování neúspěšného pokusu
            $this->getPresenter()->flashMessage('Email nebo heslo jsou špatně', 'alert-warning');

        }
    }

    public function handleSupportCollection(string $key)
    {

    }

    public function createComponentPaymentForm(): Form
    {
        $form = new PaymentFormFactory();
        $form -> setCurrency('czk');
        $form -> setMinimalAmount(100);
        $form -> setCollctionKey(intval($this->getParameter('key')));
        $return = $form -> create();
        $return -> onSuccess[] = [$this, 'paymentFormSuccess'];
        return $return;

    }

    /**
     * @throws QRPlatbaException
     */
    public function paymentFormSuccess($form, $values): void
    {
        if ($collection = $this->collectionsRepository->findOneByKey(intval($this->getPresenter()->getParameter('key')))) {
            $qr = $this->QRPlatba->setCurrency(mb_strtoupper($collection->getCurrency()));
            $qr->setMessage($values['comment']);
            $qr->setVariableSymbol(strval($collection->getCollectionKey()));
            $qr->setSpecificSymbol(strval($collection->getId()));
            $qr->setAmount($values['pay']);
            $qr->setDueDate(new \DateTime('now'));
            $qr->setAccount('112233445566/0066');//TODO: správné číslo účtu
            //$qr ->setLabel($collection->getCollectionName().': '.$values['pay']);  --kontrola instalce Freetype

            $image = $qr->getQRCodeImage();

            $payment = new Payments();
            $payment->setComment($values['comment']);
            $payment->setVariableSymbol($collection->getCollectionKey());
            $payment->setCurrency($collection->getCurrency());
            $payment->setCollections($collection);
            $payment->setCreatedAt(new DateTimeImmutable('now'));
            $payment->setFee(null); //TODO: Doplnit Feečko ze systemSetings
            $payment->setPay($values['pay']);
            $payment->setPaymentStatus(PaymentStatusEnum::Expected);
            $payment->setAzyl($collection->getAzyl());
            $this->paymentsRepository->save($payment);

            $this->getTemplate()->qr = $image;
            if ($this->isAjax()) {

                $this->redrawControl('qr');
            }
        }
        else
        {
            $this->getTemplate()->qr = 'Nastal problém s generováním QR kódu!!! ';
            $this->analyticsService->setComment('KURVA!!! chyba při generování QR kódu');
            if ($this->isAjax()) {

                $this->redrawControl('qr');
            }

        }

    }

    public function createComponentRegisterForm(): Form
    {
        $form = (new registerFormFactory($this->usersRepository, $this->entityManager, $this->getLinkGenerator()))->create();
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
       $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
       $aks = new AdoptionKeyService();
       $aks -> createKey($this->getUser()->id, $animal->getId(),$animal->getAzyl()->getId());
       $key =  $aks->getKey();
       $reciver = $this->usersRepository->getUserByAzylId($animal->getAzyl()->getId());

       $adoption  = new Adoption();
       $adoption -> setDescription($values->description);
       $adoption -> setAnimal($animal);
       $adoption -> setAdoptionKey($key);
       $adoption -> setCreatedAt(new DateTimeImmutable());
       $adoption -> setUpdatedAt(new DateTimeImmutable());
       $adoption -> setAdoptionType($animal->getAdoptionType());
       $adoption -> setActionType(ActionTypeEnum::START_ADOPTION);
       $adoption -> setUser($user);
       $adoption -> setAzyl($animal->getAzyl());
       $adoption -> setSetings('adopce');
       $adoption -> setDeleted(false);
       $adoption -> setConfirmed(false);
       $adoption -> setCanceled(false);
       $adoption -> setActionType(ActionTypeEnum::START_ADOPTION);
       $adoption -> setAdoptionType($animal->getAdoptionType());
       $adoption -> setHowMuch($values->howMuch ?: 1);

       $this->adoptionsRepository->saveAdoption($adoption);

       //poslat zprávu
        //vytvoříme si novou konverzaci
            $conversation = new Conversations();
            $conversation->setAdoption($adoption);
            $conversation->setUser($user);
            $conversation->setBlock(false);
            $conversation->setAzyl($animal->getAzyl());
            $conversation->setComment('Adopce | '.$adoption->getId());

        $this->conversationsRepository->save($conversation);

            //přidáme do ní zprávu
            $message =new Messages();
            $message -> setType(MessageTypeEnum::FROMUSER_TYPE);
            $message -> setCreatedAt(new DateTimeImmutable());
            $message -> setAdoption($adoption);
            $message -> setUser($user);
            $message -> setConversation($conversation);
            $message ->setMessage('Uživatel: '.$user->getUserName(). ' požádal o adopci zvířete: '.$animal->getName().'. Tak mu dejte co nejdřív vědět! Podrobnosti najdete'.
                '<a href="'.$this->getPresenter()->link('Azyl:adoptions',$adoption->getId()).'"> Zde</a>');
            $message->setReaded(false);

            $this->messagesRepository->save($message);


       $this->getPresenter()->redirect('this');


    }

    public function formRegisterSucceeded(Form $form, \stdClass $values):void
    {
        if(!($values->username === $this->usersRepository->getUserByUserName($values->username) || $values->email === $this->usersRepository->getUserByEmail($values->email) || $values->password === $values->password2))
        {
            $form->addError('Hesla nejsou stejná, nebo některý z údajů je již registrován!');
            $this->flashMessage('Nelze registrovat účet některý z údajů koliduje s již existujícím účtem!','alert-warning');
        }
        else {
            try {

                $now = new DateTimeImmutable();
                $token = md5($values->email.$now->format('Y-m-d H:i:s'));
                $phoneNumber = \Brick\PhoneNumber\PhoneNumber::parse($this->getRequest()->post['phone']);

                $phoneNumber->format(\Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL);
                $user = new Users();
                $user->setUserName(strval($values->username));
                $user->setEmail(strval($values->email));
                $user->setPassword($this->passwords->hash($values->password));
                $user->setRole('user');
                $user->setCreatedAt($now);
                $user->setVerified(FALSE);
                $user->setPhone(empty($this->getRequest()->post['phone']) ? null : $phoneNumber->format(\Brick\PhoneNumber\PhoneNumberFormat::INTERNATIONAL));
                $user->setLegalTerms($values->legalTerms);
                $user->setAdoptionVerification($values->adoptionVerification);
                $user->setMailverified(FALSE);
                $user->setDeleted(FALSE);
                $user->setBaned(FALSE);
                $user->setPhoneVerified(FALSE);
                $user->setMailVerifyToken($token);
                $this->usersRepository->addUser($user);

                //Kontrola jestli daná IP není na Blacklistu pokud ano tak tam uklidit
                 $fwlog = $this->firewallLogsRepository->findOneByIp($_SERVER['REMOTE_ADDR']);
                 if (!is_null($fwlog))
                 {
                 $this->firewallLogsRepository->delete($fwlog);
                 $this->flashMessage('Záznam ve Firewallu pro Vaší IP byl podmínečně odstraněn','alert-warning');

                 }
                //Send registration email


                $verificationlink = $this->link('//Home:registered', ['vrf' => $token]);


                $template = $this->templateFactory->createTemplate();
                $html = $template->renderToString(__DIR__ . '/Template/Email/RegistrationEmail.latte', ['verificationLink' => $verificationlink]);

                    $this->emailService->sendEmail(
                        'Registrace Virtuální Azyl <registration@virtualniazyl.cz>',
                        strval($values->email),
                       'Registrace na Virtuální Azyl',
                                         $html
                    );

                $this->getPresenter()->flashMessage('Registrace proběhla v pořádku :-)', 'alert-success');
                $this->getPresenter()->redirect('Home:Registered');
            } catch (AuthenticationException $e) {
                $form->addError('Registrace se nezdařila možná jméno nebo email jsou již registrovány');
            } catch (Nette\Application\UI\InvalidLinkException $e) {
            }
        }

    }

    public function createComponentAzylSendMessageForm(): Form
    {
        $form = $this->azylSendMessageFormFactory->create();
        $form->addHidden('address','address');
        $form->onSuccess[] = [$this, 'azylSendMessageFormSucceeded'];
        return $form;

    }

    public function createComponentSearchForm(): Form
    {
        $form = $this->searchFormFactory->create();
        $form->onSuccess[] = [$this, 'searchFormSucceeded'];
        return $form;
    }

    public function searchFormSucceeded(Form $form, \stdClass $values):void
    {



        $this->flashMessage('Hledání'.$values->search);
    }

    public function azylSendMessageFormSucceeded($form, \stdClass $values):void
    {
       $azyl = $this->conversationsRepository->findOneById($values->address)->getAzyl();  //adresát
       $user = $this->usersRepository->findOneBy(['id'=>$this->getUser()->getId()]); //odesilatel

        $message = new Messages();
        $message->setUser($user);
        $message->setConversation($this->conversationsRepository->findOneById($values->address));
        $message->setType(MessageTypeEnum::FROMUSER_TYPE);
        $message->setAzyl(null);
        $message->setCreatedAt(new DateTimeImmutable());
        $message->setMessage($values->message);
        $message->setReaded(FALSE);
        $this->messagesRepository->save($message);


        if($this->isAjax())
        {
            $this->getTemplate()->messageReturn = 'Zpráva odeslána &nbsp;'.$azyl->getAzylName();
            $this->flashMessage('Zpráva odeslána', 'alert-success');
        }
        else
        {
            $this->flashMessage('Zpráva odeslána '.$azyl->getAzylName(), 'alert-success');
            $this->getPresenter()->redirect('this');
        }
    }

    public function createComponentContractSignForm(): Form
    {
        $form = $this->contractSignFormFactory->create();
        $form->onSuccess[] = [$this, 'contractSignFormSucceeded'];
        return $form;

    }

    public function contractSignFormSucceeded(Form $form, \stdClass $values):void
    {
        $this->flashMessage('Smlouva je odsouhlasena', 'alert-success');
        $this->redirect('this');

    }
}