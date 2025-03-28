<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\CityDataSource;
use App\Components\Datagrids\AnimalsDatagridFactory;
use App\Components\Datagrids\NewsDatagridFactory;
use App\Forms\adoptionStateChangeFormFactory;
use App\Forms\animalFormFactory;
use App\Forms\azylSendMessageFormFactory;
use App\Forms\azylSetingsFormFactory;
use App\Forms\CollectionFormFactory;
use App\Forms\messagesFormFactory;
use App\Forms\newsFormFactory;
use App\Forms\PhotoUploadFormFactory;
use App\Forms\RegisterFormFactory;
use App\Forms\userDetailsFormFactory;
use App\Forms\userScoreFormFactory;
use App\Model\Orm\Entity\AdoptionAction;
use App\Model\Orm\Entity\AdoptionLog;
use App\Model\Orm\Entity\Animal;
use App\Model\Orm\Entity\Collections;
use App\Model\Orm\Entity\News;
use App\Model\Orm\Entity\Photo;
use App\Model\Orm\Entity\UsersRatings;
use App\Model\Orm\Enums\ActionTypeEnum;
use App\Model\Orm\Repository\AdoptionLogRepository;
use App\Model\Orm\Repository\AdoptionsRepository;
use App\Model\Orm\Repository\AnalyticsRepository;
use App\Model\Orm\Repository\AnimalsRepository;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\CityRepository;
use App\Model\Orm\Repository\CollectionsRepository;
use App\Model\Orm\Repository\ConversationsRepository;
use App\Model\Orm\Repository\MessagesRepository;
use App\Model\Orm\Repository\NewsRepository;
use App\Model\Orm\Repository\PaymentsRepository;
use App\Model\Orm\Repository\PhotosRepository;
use App\Model\Orm\Repository\UsersRatingsRepository;
use App\Model\Orm\Repository\UsersRepository;
use App\Model\Services\Menu;
use App\Repository\SpeciesRepository;
use App\Services\AnalyticsService;
use App\Services\CollectionKeyService;
use App\Services\MessagesService;
use Brick\PhoneNumber\PhoneNumberFormat;
use Brick\PhoneNumber\PhoneNumberParseException;
use Contributte\Application\UI\BasePresenter;
use DateTimeImmutable;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\NoReturn;
use libphonenumber\NumberParseException;
use Nette;
use Nette\Application\UI\Form;
use Nette\Application\UI\InvalidLinkException;
use Random\RandomException;
use Selectt\SelecttAutocompleteControl;
use Symfony\Component\VarExporter\Internal\Values;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridColumnStatusException;
use Ublaboo\DataGrid\Exception\DataGridException;
use App\Services\AzylAddressService;

class AzylPresenter extends BasePresenter
{

    private AnimalsRepository $animalsRepository;
    private AnimalFormFactory $animalFormFactory;
    private AzylSetingsFormFactory $azylSetingsFormFactory;


    public function __construct(AnimalsRepository                           $animalsRepository,
                                AnimalFormFactory                           $animalFormFactory,
                                AzylSetingsFormFactory                      $azylSetingsFormFactory,
                                public NewsRepository                       $newsRepository,
                                public NewsFormFactory                      $newsFormFactory,
                                public NewsDatagridFactory                  $newsDatagridFactory,
                                public AnimalsDatagridFactory               $animalsDatagridFactory,
                                public Photo                                $photos,
                                public PhotosRepository                     $photosRepository,
                                public SpeciesRepository                    $speciesRepository,
                                public UsersRepository                      $usersRepository,
                                public AzylRepository                       $azylRepository,
                                private MessagesRepository                  $messagesRepository,
                                private messagesFormFactory                 $messagesFormFactory,
                                private messagesService                     $messagesService,
                                private AnalyticsRepository                 $analyticsRepository,
                                private AnalyticsService                    $analyticsService,
                                private AdoptionsRepository                 $adoptionsRepository,
                                private CollectionsRepository               $collectionsRepository,
                                private CollectionFormFactory               $collectionFormFactory,
                                private collectionKeyService                $collectionKeyService,
                                private PhotoUploadFormFactory              $photoUploadFormFactory,
                                private readonly PaymentsRepository         $paymentsRepository,
                                private readonly cityRepository             $cityRepository,
                                private readonly CityDataSource             $cityDataSource,
                                private readonly RegisterFormFactory        $registerFormFactory,
                                private readonly userDetailsFormFactory     $userDetailsFormFactory,
                                private readonly azylSendMessageFormFactory $azylSendMessageFormFactory,
                                private AzylAddressService                  $azylAddressService,
                                private ConversationsRepository             $conversationsRepository,
                                private EntityManagerInterface              $entityManager,
                                private AdoptionLogRepository               $adoptionLogRepository,
                                private AdoptionStateChangeFormFactory      $adoptionStateChangeFormFactory,
                                private UserScoreFormFactory                $userScoreFormFactory,
                                private UsersRatingsRepository              $usersRatingsRepository,
    )
    {
        parent::__construct();
        $this->animalsRepository = $animalsRepository;
        $this->animalFormFactory = $animalFormFactory;
        $this->azylSetingsFormFactory = $azylSetingsFormFactory;
        $this->entityManager = $entityManager;


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
                $this->analyticsService->setComment('Azyl presenter, nepovolený přístup|' . $this->getPresenter()->getAction() . ' | ' . $this->getPresenter()->getUser()->getIdentity()->getId());
                $this->analyticsService->logVisit();
                $this->redirect('Home:default');
            } else {
                if (!is_null($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl'])) {

                    $menu = new Menu();
                    $this->getTemplate()->mainMenuItems = $menu->getMenu();
                    $this->analyticsService->setPresenter($this);
                    $this->analyticsService->setComment('Azyl presenter |' . $this->getPresenter()->getAction() . ' | ' . $this->getPresenter()->getUser()->getIdentity()->getId());
                    $this->analyticsService->logVisit();
                } else {
                    $this->getPresenter()->redirect('SuperAdmin:SetAzyl');
                }
            }
        }


    }

    /**
     * @throws NonUniqueResultException
     */
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
        $this->getTemplate()->personalPhoto = $this->photosRepository->findById($this->user->getIdentity()->getData()['User']->getPersonalPhoto());
        $this->getTemplate()->random = $this->getUser()->getIdentity()->getData()['Azyl']->getRandom();



    }


    public function handleDelete(int $id): void
    {
        $animal = $this->animalsRepository->findById($id);
        $animal->setIsDeleted(true);
        $this->animalsRepository->saveAnimal($animal);
        $this->flashMessage('Zvířátko bylo smazáno.', 'alert-success');
        $this->redirect('this');
    }


    public function createComponentAdoptionStateForm(): Form
    {
        $form = $this->adoptionStateChangeFormFactory->create();
        $form->onSuccess[] = [$this, 'adoptionStateChangeFormSubmitted'];
        return $form;
    }

    public function adoptionStateChangeFormSubmitted(Form $form, \stdClass $values): void
    {
        $log = new AdoptionLog();
        $adoption = $this->adoptionsRepository->findOneBy(['id' => intval($this->getPresenter()->getParameter('id'))]);
        $animal = $this->animalsRepository->findOneBy(['id'=>$adoption->getAnimal()->getId()]);
        $adoption->setUpdatedAt(new DateTimeImmutable());
        $log->setAdoption($adoption);
        $log->setCreatedAt(new DateTimeImmutable());

            if ($form['comment']->isSubmittedBy())  //jen komentář
                {
                    $log->setComment($values->commentText);
                    $log->setActionType($adoption->getActionType());
                    $this->flashMessage('Komentář k adopci přidán.','alert-primary');
                }
            elseif($form['writ']->isSubmittedBy()) //písemný kontakt
                {
                    $log->setComment($values->commentText);
                    $animal->setAdopted(false);
                    $animal->setToAdoption(true);
                    $adoption->setActionType(ActionTypeEnum::CONTACT_ADOPTION);
                    $log->setActionType(ActionTypeEnum::CONTACT_ADOPTION);
                    $this->flashMessage('Písemný kontakt.','alert-primary');
                }
            elseif($form['phon']->isSubmittedBy()) //telefonický kontakt
                {
                    $log->setComment($values->commentText);
                    $animal->setAdopted(false);
                    $animal->setToAdoption(true);
                    $adoption->setActionType(ActionTypeEnum::PHONE_CALL_ADOPTION);
                    $log->setActionType(ActionTypeEnum::PHONE_CALL_ADOPTION);
                    $this->flashMessage('Telefonický kontakt.','alert-primary');
                }
            elseif($form['pers']->isSubmittedBy()) //osobní kontakt
                {
                    $log->setComment($values->commentText);
                    $animal->setAdopted(false);
                    $animal->setToAdoption(true);
                    $adoption->setActionType(ActionTypeEnum::PERSONAL_VISIT_ADOPTION);
                    $log->setActionType(ActionTypeEnum::PERSONAL_VISIT_ADOPTION);
                    $this->flashMessage('Osobní kontakt.','alert-primary');
                }
            elseif($form['pre']->isSubmittedBy()) //předschválení adopce
                {
                    $log->setComment($values->commentText);
                    $animal->setAdopted(false);
                    $animal->setToAdoption(false);
                    $adoption->setActionType(ActionTypeEnum::VERIFICATION_ADOPTION);
                    $log->setActionType(ActionTypeEnum::VERIFICATION_ADOPTION);
                    $this->flashMessage('Pro adopci byla vystavena smlouva a adoptující byl vyzván aby podepsal smlouvu a adopční podmínky','alert-success');
                }
            elseif($form['ok']->isSubmittedBy()) //potvrzení adopce
                {
                    $log->setComment($values->commentText);
                    $animal->setAdopted(true);
                    $animal->setToAdoption(false);
                    $adoption->setActionType(ActionTypeEnum::POSITIVE_ADOPTION_END);
                    $log->setActionType(ActionTypeEnum::POSITIVE_ADOPTION_END);
                    $this->flashMessage('Super! Adopce dobře dopadlo... paráda na světě je zase o něco víc lásky! :-)','alert-success');
                }
            elseif($form['stop']->isSubmittedBy()) //zrušení adopce
                {
                    $log->setComment($values->commentText);
                    $animal->setAdopted(false);
                    $animal->setToAdoption(true);
                    $adoption->setActionType(ActionTypeEnum::NEGATIVE_ADOPTION_END);
                    $log->setActionType(ActionTypeEnum::NEGATIVE_ADOPTION_END);
                    $this->flashMessage('Adopce zastavena','alert-warning');
                }

        $this->animalsRepository->saveAnimal($animal);
        $this->adoptionsRepository->saveAdoption($adoption);
        $this->adoptionLogRepository->save($log);
        if($this->isAjax())
        {
            $this->redrawControl('adoptionInteraction');
        }
        else
        {
            $this->redirect('this');
        }
    }

    public function createComponentUserScoreForm(): Form
    {
       $form = $this->userScoreFormFactory->create();
        $form->onSuccess[] = [$this, 'userScoreFormSubmitted'];
        return $form;

    }

    public function userScoreFormSubmitted(Form $form, \stdClass $values): void
    {
        $userRating = new UsersRatings();
        $userRating->setReviewer($this->usersRepository->getUserById($this->getUser()->getId()));
        $userRating->setCreatedAt(new DateTimeImmutable());
        $userRating->setAzyl($this->azylRepository->findOneBy(['id' => $this->getUser()->getIdentity()->getData()['Azyl']->getId()]));
        $userRating->setUser($this->adoptionsRepository->findOneBy(['id' => intval($this->getParameter('id'))])->getUser());
        if ($form['1']->isSubmittedBy())
        {
            $userRating->setRating(1);
        }
        elseif($form['2']->isSubmittedBy())
        {
            $userRating->setRating(2);
        }
        elseif($form['3']->isSubmittedBy())
        {
            $userRating->setRating(3);
        }
        elseif($form['4']->isSubmittedBy())
        {
            $userRating->setRating(4);
        }
        elseif($form['5']->isSubmittedBy())
        {
            $userRating->setRating(5);
        }

        $userRating->setReview($values->comment);
        $this->usersRatingsRepository->save($userRating);

        $this->flashMessage('Hodnocení adoptujícího uloženo',' alert-success');
        if($this->isAjax())
        {
            $this->redrawControl('rating');
        }
        else
        {
            $this->redirect('this');
        }
    }

    public function handleUserReview($user,$r):void
    {

        $this->flashMessage('Hodnocení uloženo','alert-success');
    }

    public function renderDefault(): void
    {
        if(empty($this->getUser()->getIdentity()->getData()['Azyl']->getAzylName()))
        {
            $this->flashMessage('Nejprve nastavte základní informace o Vašem azylu (nejsou stejné jako vaše uživatelská nastavení), důležitý je název nějaký smypatický popis a kontakt
                                         především město, podle něj se dá vyhodnotit jak blízko jste k zájemcům o adopci!', 'alert-warning');

            $this->redirect('Azyl:settings');

        }

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
        if ($key === null) {
        $this->getTemplate()->collectionsNoActive = $this->collectionsRepository->findByAzylNoActive($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
        $this->getTemplate()->collections = $this->collectionsRepository->findByAzylActive($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
        $this->getTemplate()->collectionsWaiting = $this->collectionsRepository->findByAzylWaiting($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
        }
        else
        {
            $this->getTemplate()->collectionsNoActive = $this->collectionsRepository->findByAzylNoActive($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
            $this->getTemplate()->collections = $this->collectionsRepository->findByAzylActive($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
            $this->getTemplate()->collectionsWaiting = $this->collectionsRepository->findByAzylWaiting($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
            $this->getTemplate()->paymentsAll = $this->paymentsRepository->findBy(['variableSymbol'=> $key, 'azyl' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']]);
        }
    }

    public function handleStopCollection(int $key): void //TODO: platby a stopování sbírky
    {
        $collection = $this->collectionsRepository->findOneByKey(intval($key));
        $collection->setIsActive(false); //nastavit na vypnuto
        $this->collectionsRepository->save($collection);
        $this->flashMessage('Sbírka byla nastavena na neaktivní','alert-success');
        if ($this->isAjax())
        {
            $this->redrawControl();
        }
        else
        {
            $this->redirect('this');
        }

    }

    public function handleCollectionPayments(int $key): void
    {
        $payments = $this->collectionsRepository->findOneByKey(intval($key))->getPayments();
        $this->getTemplate()->payments = $this->collectionsRepository->findOneByKey(intval($key))->getPayments();
        if ($this->isAjax()) {
            $this->getPresenter()->redrawControl();
        }
    }

    public function createComponentCollectionForm(): Form
    {
        $form = $this->collectionFormFactory->create();
        $form ->removeComponent($form->getComponent('send'));
        $form ->addSubmit('send','Uložit sbírku')->setHtmlAttribute('class', 'btn btn-primary');

        $form->onSuccess[] = [$this, 'collectionFormSucceeded'];

        if (is_null($this->getPresenter()->getParameter('key'))) {
            return $form;
        } else {
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
        if (is_null($this->getPresenter()->getParameter('key'))) {
            $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
            $user = $this->usersRepository->findOneBy(['id' => $this->getPresenter()->getUser()->getIdentity()->getData()['User']->getId()]);
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
            $collection->setExtend($values['extend']);
            $collection->setStartAt($values['startAt']);
            $collection->setIsActive($values['isActive']);
            $collection->setApproved(false);
            $this->collectionsRepository->save($collection);
            $collection->setCollectionKey($this->collectionKeyService->createCollectionKey($azyl->getId(), $collection->getId()));

            if ($values['headline']->hasFile()) {
                $photo = new Photo();
                $photo->setAzyl($azyl);
                $photo->setCollections($collection);
                $photo->setUser($user);
                $photo->setDate(new DateTimeImmutable());
                $photo->uploadCollectionHeadlinePhoto($values['headline']);
                $this->photosRepository->save($photo);
                $collection->setPhoto($photo);
            }
            $this->collectionsRepository->save($collection);
            $this->flashMessage('Sbírka byla uložena, pokud je datum nastavené na dnešek ihned se spustí', 'alert-success');
            $this->getPresenter()->redirect('Azyl:Collections');
        } else {
            $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
            $user = $this->usersRepository->findOneBy(['id' => $this->getPresenter()->getUser()->getIdentity()->getData()['User']->getId()]);
            $collection = $this->collectionsRepository->findOneByKey(intval($this->getPresenter()->getParameter('key')));
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
            $collection->setExtend($values['extend']);
            $collection->setStartAt($values['startAt']);
            $collection->setIsActive($values['isActive']);
            $collection->setApproved(false);
            if ($values['headline']->hasFile()) {

                $photo = new Photo();
                $photo->setAzyl($azyl);
                $photo->setCollections($collection);
                $photo->setUser($user);
                $photo->setDate(new DateTimeImmutable());
                $photo->uploadCollectionHeadlinePhoto($values['headline']);
                $this->photosRepository->save($photo);
                $collection->setPhoto($photo);
                }
              else
                 {
                $collection->setPhoto(null);
                }

            $this->collectionsRepository->save($collection);
            $this->flashMessage('Sbírka byla uložena, pokud je datum nastavené na dnešek ihned se spustí', 'alert-success');
            $this->getPresenter()->redirect('Azyl:Collections');
        }
    }

    public function renderProfil(): void
    {
        $user = $this->usersRepository->getUserById($this->getUser()->getId());
        $photos = $user->getPhotos();

        $this->getTemplate()->title = 'Uživatelský Profil';
        $this->getTemplate()->personalPhoto = $this->photosRepository->findById($user->getPersonalPhoto());
        $this->getTemplate()->adoptions = empty($user->getAdoptions()) ? null : $user->getAdoptions();
        $this->getTemplate()->photos = $photos;

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

    public function actionAnimal(?int $id = null): void
    {
        if ($id === null) {
            $this->getTemplate()->title = 'Azyl - Přidání nového zvířátka';
        } else {
            if (!$animal = $this->animalsRepository->findById($id)) {
                $this->getTemplate()->title = 'Azyl - Přidání nového zvířátka';

            } else {
                $this->getTemplate()->photos = $animal->getPhotos();
                if ($animal->getAdoption()) {
                    $this->getTemplate()->adoptions = $animal->getAdoption();
                }
                $this->getTemplate()->title = 'Azyl - Editace zvířátka';
                $this['animalForm']->setDefaults($animal);
            }
        }
    }

    public function actionMessages(?string $id): void
    {
        $this->getTemplate()->title = 'Zprávy';
        $azyl = $this->azylRepository->findOneBy(['id' => $this->getUser()->getIdentity()->getData()['Azyl']->getId()]);

        $chats = $this->conversationsRepository->findByAzyl($azyl);

        $this->getTemplate()->chats = $chats;
        $this->redrawControl('contacts');
        $this->redrawControl('messagesCount');
        $this->redrawControl('messages');
    }

    public function actionAdoptions(?int $id): void
    {

        if (!is_null($id)) {
            $adoption = $this->adoptionsRepository->findOneBy(['id' => $id]);
            $this->getTemplate()->adoption = $adoption;

        } else {

            $adoptions = $this->adoptionsRepository->findBy(['azyl' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']], ['updatedAt' => 'DESC']);

            $this->getTemplate()->adoptions = $adoptions;
        }

    }


    public function handleChat(string $id): void
    {

        $messagesSource = $this->messagesRepository->findBytConversationMessages($id);


        $this->getTemplate()->messages = $messagesSource;
        $this->getTemplate()->conversation = $id;

        $this->messagesService->markMessagesAsRead($id);

        if ($this->isAjax()) {
            $this->redrawControl('messagesCount');
            $this->redrawControl('chats');
            $this->redrawControl('messages');
        }
    }

    public function handleDeleteMsg(int $id): void
    {
        $redirectAddress = $this->messagesRepository->getMessagesById($id)->getReceiverAddress();
        if ($this->messagesService->deleteMessage($id, $this->getPresenter())) {
            $this->flashMessage('Vzkaz byl smazán.', 'alert-success');
        } else {

            $this->flashMessage('Při mazání vzkazu nastala chyba.', 'alert-danger');
        }
        if ($this->isAjax()) {
            $this->redrawControl('messagesCount');
            $this->redrawControl('chats');
            $this->redrawControl('messages');
        } else {
            $chat = "?do=chat";
            $url = $this->link('Azyl:messages', $redirectAddress) . $chat;
            $this->redirectUrl($url);
        }

    }

    public function messagesFormSucceeded(Form $form, \stdClass $values): void
    {
        $this->getPresenter()->isAjax();
        $this->messagesService->messagesFormSucceeded($form, $values, $this);

        if ($this->isAjax()) {
            $this->redrawControl('messages');
        } else {
            $chat = "?do=chat";
            $url = $this->link('this', $values->id) . $chat;
            $this->redirectUrl($url);

        }

    }

    public function actionNews(): void
    {
        $this->getTemplate()->title = 'News';
    }

    public function renderPhotos(): void //TODO: Zobrazení Fotek azylu
    {
        $this->getTemplate()->title = 'Photos';
        $this->getTemplate()->basepath = '';
        $this->getTemplate()->photos = $this->photosRepository->fetchByAzylId($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
    }

    public function renderMessages(): void
    {
        $this->getTemplate()->title = 'Message';
    }

    public function renderSettings(): void
    {
        $this->getTemplate()->title = 'Settings';
    }


    public function renderAdoptions(): void
    {
        $this->getTemplate()->title = 'Adoptions - Azyl: ' . $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getAzylName();

    }

    // Actions

    // Handle
    public function handleDeleteAzylPhoto(int $photoId): void  // označí fotku jako smazanou kontroluje ID fotky a ID azylu, aby nemohl fotku smazat někdo jiný podvrhnutím IDfotky
    {
        $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
        $photo = $this->photosRepository->findOneBy(['id' => $photoId, 'azyl' => $azyl]);
        if(!empty($photo))
        {
            $photo->setDeleted(true);
            $this->photosRepository->save($photo);
            $this->flashMessage('Fotka smazána', 'alert-success');
            if ($this->isAjax()) {

                $this->redrawControl('photos');
            }
            else
            {
                $this->redirect('this');
            }
        }
        else
        {
            $this->flashMessage('Fotku nelze smazat', 'alert-danger');
            if ($this->isAjax()) {

                $this->redrawControl('photos');
            }
            else
            {
                $this->redirect('this');
            }

        }
    }

    public function handleSetHomeAzylPhoto(int $id): void //nastaví fotku jako hlavní fotku Azylového profilu kontorluje to azyl podle profilu a id fotky
    {
        $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
        $photo = $this->photosRepository->findOneBy(['id' => $id, 'azyl' => $azyl]);

        if(!empty($photo))
        {
                        $azyl->setMainPhoto($photo->getId());
                        $this->azylRepository->saveAzyl($azyl);
                        $this->flashMessage('Fotka nastavena', 'alert-success');
        }
        else
        {
            $this->flashMessage('Fotku nelze nastavit', 'alert-danger');

        }

    }


    public function handleNewsDelete(?int $id): void
    {
        $news = $this->newsRepository->findOneBy(['id' => $id, 'azyl' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']]);
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
        if ($this->getPresenter()->getParameter('id') !== null) {
          //  $animal = $this->animalsRepository->findById(intval($this->getPresenter()->getParameter('id')));
            $animal = $this->animalsRepository->findOneBy(['id' => intval($this->getPresenter()->getParameter('id')), 'azyl' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']]);
            $form->setDefaults([
                    'name' => $animal->getName(),
                    'description' => $animal->getDescription(),
                    'species' => $animal->getSpecies()->getId(),
                    'birthDate' => !is_null($animal->getBirthdate()) ? $animal->getBirthdate()->format('d-m-Y') : null,
                    'reception' => !is_null($animal->getReception()) ? $animal->getReception()->format('d-m-Y') : null,
                    'breed' => $animal->getBreed(),
                    'toAdoption' => $animal->isToAdoption(),
                    'adoptionType' => $animal->getAdoptionType(),
                    'multiAdoption' => $animal->getMultiAdoption(),
                    'signed' => !is_null($animal->getSigned()) ? $animal->getSigned() : 'no',
                    'weight' => $animal->getWeight(),
                    'height' => $animal->getHeight(),
                    'howMuch' => $animal->getHowMuch()]
            );
        }
        return $form;
    }

    /**
     * @throws InvalidLinkException
     * @throws NonUniqueResultException
     */
    public function createComponentAzylSettingsForm(): Form
    {
        $formDef = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
        $city = $this->cityRepository->findCityById(intval($formDef->getCity()));
        $factory = $this->azylSetingsFormFactory;
        $factory->setLink($this->link('Json:select2'));
        $form = $factory->create();
        if($city !== null){
            $form['city']-> setItems([$city->getId() => $city->getCityName()], true);
        }

        $form->setDefaults([
            'id' => $formDef->getId(),
            'azylName' => $formDef->getAzylName(),
            'description' => $formDef->getDescription(),
            'bankAccount' => $formDef->getBankAccount(),
            'bankCode' => $formDef->getBankCode(),
            'bankSpecificCode' => $formDef->getBankSpecificCode(),
            'phoneNumber' => $formDef->getPhoneNumber(),
            'city' => $formDef->getCity(),
            'web' => $formDef->getWeb(),
            'email' => $formDef->getEmail(),
            'ico' => $formDef->getIco(),
            'shortDescription' => $formDef->getShortDescription(),
        ]);

        $form->onSuccess[] = [$this, 'azylSettingsFormSucceeded'];
        return $form;
    }

    #[NoReturn] public function azylSettingsFormSucceeded(Form $form, \stdClass $values): void
    {
        $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());

        $azyl->setAzylName($values->azylName);
        $azyl->setDescription($values->description);
        $azyl->setBankAccount($values->bankAccount);
        $azyl->setBankCode($values->bankCode);
        $azyl->setBankSpecificCode($values->bankSpecificCode);
        $azyl->setPhoneNumber($values->phoneNumber);
        $azyl->setEmail($values->email);
        $azyl->setWeb($values->web);
        $azyl->setIco($values->ico);
        $azyl->setShortDescription($values->shortDescription);
        $azyl->setCity(intval($this->getRequest()->getPost('city')));
        if(is_null($azyl->getMessageAddress()))
        {
            $azyl->setMessageAddress($this->azylAddressService->generateCommunicationAddress($azyl->getId(), $azyl->getEmail(), $azyl->getAzylName()));
            $this->flashMessage('POZOR! Nastavena komunikační adresa interního systému.','alert-warning');
        }

        $this->azylRepository->saveAzyl($azyl);
        $this->flashMessage('Nastavení azylu bylo aktualizováno.', 'alert-success');
        $this->redirect('this');
    }

    /**
     * @throws NonUniqueResultException
     */
    #[NoReturn] public function animalFormSucceeded(Form $form, $values): void
    {

        $id = $this->getParameter('id');
        //todo: ověření práv uživatele na úpravu zvířátka
        if (is_null($id)) {

            $animal = new Animal();
            $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
            $city = $this->cityRepository->findCityById(intval($azyl->getCity()));
            $cityName = $city->getCityName();
            $region = $city->getRegion();
            $office = $city->getCityOffice();

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
            $animal->setSigned($values->signed);
            $animal->setHeight($values->height);
            $animal->setWeight($values->weight);
            $animal->setMultiAdoption($values->multiAdoption);
            $animal->setReception($values->reception);
            $tags = $cityName.' '.$region.' '.$office.' '.$values->name.' '.$values->breed.' '.$this->speciesRepository->findOneById($values->species)->getName();
            $animal->setTags($tags);
            $this->animalsRepository->persist($animal);
            foreach ($values->photos as $photo) {

                $photoUpload = new Photo();
                $photoUpload->setAzyl($azyl);
                $photoUpload->setDate(new DateTimeImmutable('now'));
                $photoUpload->setAnimal($animal);
                $photoUpload->uploadAzylPhoto($photo);
                $this->photosRepository->save($photoUpload);
            }
            $this->animalsRepository->flush($animal);
            $this->flashMessage('Zvířátko bylo úspěšně přidáno.', 'alert-success');
            $this->redirect('Azyl:animals');
        } else {

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
            $animal->setSigned($values->signed);
            $animal->setHeight($values->height);
            $animal->setWeight($values->weight);
            $animal->setReception($values->reception);

            foreach ($values->photos as $photo) {
                $azyl = $this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId());
                $photoUpload = new Photo();
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
         $pined = $form->getComponent('pined');
         $form->removeComponent($pined);

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

                $form->onSuccess[] = [$this, 'newsFormSucceededUpdate'];
                return $form;

            } else {
                $this->flashMessage('Novinka nebyla nalezena.', 'alert-danger');
                $this->redirect('Azyl:news');
            }
        } else {

            $form->onSuccess[] = [$this, 'newsFormSucceeded'];

            return $form;
        }
    }

    public function createComponentMessagesForm(): Form
    {
        $form = $this->messagesFormFactory->create();
        $form->onSuccess[] = [$this, 'messagesFormSucceeded'];
        return $form;
    }


    //TODO: tohle by se mělo překopat a zpřehlednit je tu bordel!!!!
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
        $news->setPined(false);

        $this->newsRepository->save($news);


        $this->flashMessage('Novinka byla uložena.', 'alert-success');
        $this->redirect('Azyl:news');

    }

    //todo: Tady je BUG z nějakého důvodu se zobrazují i smazané novinky !!!! URGENT!!!!
    public function newsFormSucceededUpdate(Form $form, \stdClass $values): void
    {
        $id = $this->getPresenter()->getParameter('id');
        if ($id !== null) {
            $news = $this->newsRepository->findOneBy(['id' => $id]);

            if ($news) {
                $azyl = $this->azylRepository->findOneBy(['id' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId()]);

                $news->setTitle($values->title);
                $news->setContent($values->content);
                $news->setGlobal($values->global);
                $news->setVisibleFrom($values->visibleFrom);
                $news->setUpdatedAt(new DateTimeImmutable());
                $news->setImportant($values->important);
                $news->setPined(false);
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


        $grid->removeColumn('pined');
        $azyl = $this->azylRepository->findOneBy(['id' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId()]);

        $grid->setDataSource($azyl->getNews());
        return $grid;
    }

    public function createComponentAnimalsAzylDatagrid(): DataGrid

    {
        $grid = new AnimalsDatagridFactory($this->animalsRepository);
        $grid->setPresenter($this->getPresenter());

        $grid->create(); // upraví instanci, nepřepíše ji novým objektem
        $grid->setDataSource($this->animalsRepository->findBy(['azyl' => $this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']]));
        return $grid;

    }

    public function createComponentAzylPhotoUploadForm(): Form
    {
        $factory = new PhotoUploadFormFactory();
        $form = $factory->create();
        $form-> onSuccess[] = [$this, 'photoUploadFormSucceeded'];
        return $form;
    }

    public function photoUploadFormSucceeded(Form $form, \stdClass $values): void
    {
        foreach ($values->photos as $photoFile) {

            $photo = new Photo();
            $photo->setAzyl($this->azylRepository->findById($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']->getId()));
            $photo->setUser($this->usersRepository->findOneBy(['id' => $this->getPresenter()->getUser()->getId()]));
            $photo->setDate(new DateTimeImmutable());
            $photo->uploadAzylPhoto($photoFile);
            $this->photosRepository->save($photo);
            $this->flashMessage('Fotka <b>'.$photo->getOriginalName().'</b> se nahrála úspěšně', 'alert-success');

        }

        if ($this->isAjax())
        {

            $this->redrawControl('photos');
        }
        else
        {
            $this->redirect('Azyl:photos');
        }

    }

    public function createComponentUserDetailsForm(): Form
    {
        $factory = $this->userDetailsFormFactory;
        $factory->setLink($this->link('Json:select2'));
        $form = $factory->create($this->getPresenter());
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
        $city = $this->cityRepository->findOneBy(['id'=>$user->getCity()]);
        if (!is_null($user->getCity())) {
            $form['city']->setItems([$city->getId() => $city->getCityName()], true);
        }



        $form->setDefaults(['firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'phone' => $user->getPhone(),
            'street' => $user->getStreet(),
            'city' => is_null($user->getCity()) ? null : $city->getId(),
            'orientation' => $user->getOrientationNumber(),
            'house' => $user->getHouseNumber(),
            'description' => $user->getDescription()
        ]);

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
            $user->setCity( empty($post['city']) ? null : intval($post['city']));
            // $user->setCity($this->cityRepository->findCityById(intval($post['city'])));
            $this->usersRepository->save($user);
            $this->flashMessage('Uživatelské informace aktualizovány.', 'alert-success');

        }
        if($this->isAjax()){
            $this->redrawControl('citySelect');
        }
        else
        {
            $this->redirect('this');
        }

    }


    public function createComponentUserUpdateForm(string $name): Form
    {
        $form = $this->registerFormFactory->create();
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());

        $form->addUpload('personalPhoto','Profilová fotka');
        $pass = $form->getComponent('password');
        $pass->setRequired(false);
        $pass2 = $form->getComponent('password2');
        $pass2->setRequired(false);
        $form->removeComponent($form->getComponent('phone'));
        $form->removeComponent($form->getComponent('legalTerms'));
        $form->removeComponent($form->getComponent('send'));
        $form->removeComponent($form->getComponent('adoptionVerification'));
        $form->removeComponent($form->getComponent('email'));
        $form->addEmail('email', 'Email')
            ->addRule(Nette\Forms\Form::Email, 'Zadejte platný email.')
            ->addRule(function ($input) {
                $existingUser = $this->usersRepository->findOneBy(['email' => $input->value]);
                return !$existingUser || $existingUser->getId() === $this->getPresenter()->getUser()->getId();
            }, 'Tento email je již registrován.');
        $form->setDefaults($user->toArray());
        $form->addSubmit('update','Aktualizovat');
        $form->onSuccess[] = [$this, 'userUpdateFormSucceeded'];
        return $form;
    }


    #[NoReturn] public function userUpdateFormSucceeded(Form $form, \stdClass $values) : void
    {
        $user = $this->usersRepository->getUserById($this->getPresenter()->getUser()->getId());
        $user->setUpdatedAt(new DateTimeImmutable('now'));
        $user->setUpdatedBy($user);

        if($user->getEmail() !== $values->email) {
            $sendUserEmail = $this->usersRepository->findOneBy(['email' => $values->email]);

            if ($this->getPresenter()->getUser()->getId() == $sendUserEmail->getId())
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
        if ($values->personalPhoto->hasFile())
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
        $form['photos']->setHtmlAttribute('class', 'btn btn-outline-success form-control');
        $form['send']->setHtmlAttribute('class','btn btn-outline-success form-control');

        $form->onSuccess[] = [$this, 'ownerPhotoUploadFormSucceeded'];
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


}