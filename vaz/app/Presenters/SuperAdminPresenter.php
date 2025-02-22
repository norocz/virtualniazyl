<?php
declare(strict_types=1);

namespace App\Presenters;


use App\Components\Datagrids\CollectionsDatagridFactory;
use App\Forms\collectionFormFactory;
use App\Forms\systemSettingsFormFactory;
use App\Model\Orm\Entity\SystemSettings;
use App\Model\Orm\Repository\AnalyticsRepository;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\CollectionsRepository;
use App\Model\Orm\Repository\FirewallLogsRepository;
use App\Model\Orm\Repository\PhotosRepository;
use App\Model\Orm\Repository\SystemSetingsRepository;
use App\Services\IpInfoService;
use Contributte\Application\UI\BasePresenter;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Nette\Application\UI\Form;
use App\Forms\setAzylFormFactory;
use Nette\Security\AuthenticationException;
use Nette\Security\SimpleIdentity;
use Nette\Utils\Paginator;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class SuperAdminPresenter extends BasePresenter
{

    private setAzylFormFactory $setAzylFormFactory;
    private AzylRepository $azylRepository;
    private FirewallLogsRepository $firewallLogsRepository;
    private analyticsRepository $analyticsRepository;
    private ipInfoService $ipInfoService;
    private systemSettingsFormFactory $systemSettingsFormFactory;
    private collectionsRepository $collectionsRepository;
    private collectionFormFactory $collectionFormFactory;
    private collectionsDatagridFactory $collectionsDatagridFactory;
    private PhotosRepository $photosRepository;
    private SystemSetingsRepository $systemSetingsRepository;
    public function __construct(setAzylFormFactory $setAzylFormFactory,
                                azylRepository $azylRepository,
                                firewallLogsRepository $firewallLogsRepository,
                                analyticsRepository $analyticsRepository,
                                ipInfoService $ipInfoService,
                                systemSettingsFormFactory $systemSettingsFormFactory,
                                collectionFormFactory $collectionFormFactory,
                                CollectionsRepository $collectionsRepository,
                                PhotosRepository     $photosRepository,
                                CollectionsDatagridFactory $collectionsDatagridFactory,
                                SystemSetingsRepository $systemSetingsRepository,)
    {
        parent::__construct();
        $this->setAzylFormFactory = $setAzylFormFactory;
        $this->azylRepository = $azylRepository;
        $this->firewallLogsRepository = $firewallLogsRepository;
        $this->analyticsRepository = $analyticsRepository;
        $this->ipInfoService = $ipInfoService;
        $this->systemSettingsFormFactory = $systemSettingsFormFactory;
        $this->collectionsRepository = $collectionsRepository;
        $this->collectionFormFactory = $collectionFormFactory;
        $this->photosRepository = $photosRepository;
        $this->collectionsDatagridFactory = $collectionsDatagridFactory;
        $this->systemSetingsRepository = $systemSetingsRepository;

    }

    public function startup():void
    {
        parent::startup();

            if (!$this->getPresenter()->user->isLoggedIn() && !$this->getPresenter()->getUser()->isInRole('superadmin')) {
            $this->getPresenter()->redirect('Home:default');
        }


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
    }
    public function renderDefault(): void
    {
        $this->template->title = 'Admin';
    }

    public function renderAnalytics(int $page = 1):void
    {

        $paginator = new Paginator();
        $paginator->setItemCount($this->analyticsRepository->countAll());
        $paginator->setItemsPerPage(16);
        $paginator->setPage($page);
        $paginator->setBase(1);
        $this->getTemplate()->paginator = $paginator;
        $this->getTemplate()->analytics = $this->analyticsRepository->findBy([], ['id' => 'DESC'],$paginator->getLength(),$paginator->getOffset());
    }

    /**
     * @throws \Throwable
     */
    public function handleIpInfo(int $id):void
    {
        $ip='';
        $ipInfo = [];
        $ip = $this->analyticsRepository->findOneBy(['id' => $id]);
        if($this->isAjax()) {
            $ipInfo = $this->ipInfoService->getIpInfo($ip->getIpAdress());
           $this->getTemplate()->ipInfo = $ipInfo;

           $this->redrawControl('ipInfoIp'.$ip->getIpAdress());
           $this->redrawControl('ipInfoTable');

        }
    }

    public function actionCollections(?int $id = null): void
    {
        $this->getTemplate()->collections = $this->collectionsRepository->findByAzyl($this->getPresenter()->getUser()->getIdentity()->getData()['Azyl']);
    }

    public function renderSetAzyl(): void
    {
        $this->template->title = 'Nastavení azylu';
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

    public function renderOwner(): void
    {
        $this->template->title = 'Owner';
    }

    public function renderSystemSettings(): void
    {
        $this->template->title = 'System Settings';
    }

    public function renderSendmessages(): void
    {
        $this->template->title = 'Sendmessage';
    }

    public function renderAdoptions(): void
    {
        $this->template->title = 'Adoptions';
    }

    public function renderSignIn(): void
    {
        $this->template->title = 'SignIn';
    }

    public function renderFirewall(): void
    {
        $this->getTemplate()->title = 'Firewall setings';
        $this->getTemplate()->firewallLogs = $this->firewallLogsRepository->findAll();
    }
    //handle

    public function handleFirewallLogDelete($id): void
    {
        $firewallLog = $this->firewallLogsRepository->find($id);
        $ip = $firewallLog->getIp();
        $this->firewallLogsRepository->delete($id);


        if($this->getPresenter()->isAjax()){
            $this->getPresenter()->redrawControl('firewallTable');
            $this->getPresenter()->flashMessage('Záznam Firewallu ID: '.$id.' k IP:'.$ip.' smazán.');
        }
    }

    public function handleFirewallLogAddToUFW($id): void
    {
        //todo: doplnit přidní do UFW na serveru
        //csudo visudo

        //www-data ALL=(ALL) NOPASSWD: /usr/sbin/ufw

        //private function blockIpInUbuntuFirewall(string $ip): void
        //{
        //    exec("sudo ufw deny from $ip");
        //}



        $firewallLog = $this->firewallLogsRepository->find($id);
        $firewallLog->setAction('firewall_blocked');
        $ip = $firewallLog->getIp();
        $this->firewallLogsRepository->save($firewallLog);


        if($this->getPresenter()->isAjax()){
            $this->getPresenter()->redrawControl('firewallTable');
            $this->getPresenter()->flashMessage('Záznam přidán do Ubuntu Firewallu zablokovány porty 80 a 443.');
        }

    }


    public function handleFirewallLogBlock($id): void
    {

        $firewallLog = $this->firewallLogsRepository->find($id);
        $firewallLog->setAction('blocked');
        $ip = $firewallLog->getIp();
        $this->firewallLogsRepository->save($firewallLog);


        if($this->getPresenter()->isAjax()){
            $this->getPresenter()->redrawControl('firewallTable');
            $this->getPresenter()->flashMessage('Přihlášení z IP:'.$ip.' adresy zablokováno.');
        }
    }


    //components

    public function createComponentSetAzylForm(): Form
    {
        $form = $this->setAzylFormFactory->create();
        $form->onSuccess[] = [$this, 'azylSetFormSuccessed'];
        return $form;

    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function createComponentSystemSettingsForm(): Form
    {
        $setings = $this->systemSetingsRepository->lastSetings();
        $form = $this->systemSettingsFormFactory->create();
        $form -> setDefaults([
            'fee' => $setings->getFee(),
            'dph' => $setings->getDph(),
            'language' => $setings->getLanguage(),
            'payOutInterval' => $setings->getPayOutInterval(),
            'depricated' => $setings->getDepricated(),
            'relevantFrom' => $setings->getRelevantFrom(),
            'cron' => $setings->isCron(),
            'analyticsGarbage' => $setings->isAnalyticsGarbage(),
            'databaseClear' => $setings->isDatabaseClear(),
            'dphUse' => $setings->isDphUse(),
            'lasPayOut' => $setings->getLastPayOut(),
            'nextPayOut' => $setings->getNextPayOut(),

        ]);
        $form->onSuccess[] = [$this, 'systemSetingsFormSuccessed'];
        return $form;
    }

    /**
     * @throws DataGridException
     */
    public function createComponentCollectionDatagrid(): Datagrid
    {
        return $this->collectionsDatagridFactory->create();
    }

    /**
     * @throws AuthenticationException
     * @throws NonUniqueResultException
     */
    public function azylSetFormSuccessed(Form $form, \stdClass $values) : void
    {

        $azyl = $this->azylRepository->findById($values->azyl);
        $identity = $this->getUser()->getIdentity();
        if ($identity) {

            $newData = $identity->getData();
            $newData['Azyl'] = $azyl;
            $photoId = $azyl->getMainPhoto()?->getId();
            $photo = $photoId ? $this->photosRepository->findById($photoId) : null;
            $newData['Azyl']->setMainPhoto($photo);
            $newIdentity = new SimpleIdentity($identity->getId(),$identity->getRoles(),$newData);
            $newIdentity->getData()['Azyl']->setMainPhoto($photo);
            $this->user->login($newIdentity);

            $this->getPresenter()->redirect('Azyl:default');

        }

    }

    /**
     * @throws \DateMalformedStringException
     */
    public function systemSetingsFormSuccessed(Form $form, \stdClass $values) : void
    {
        $systemSetings = new SystemSettings();
        $systemSetings ->setFee($values->fee);
        $systemSetings ->setCreatedAt(new DateTimeImmutable());
        $systemSetings ->setDph($values->dph);
        $systemSetings ->setCron($values->cron);
        $systemSetings ->setDphUse($values->dphUse);
        $systemSetings ->setNextPayOut(new DateTimeImmutable($values->nextPayOut->format('Y-m-d H:i:s')));
        $systemSetings ->setPayOutInterval($values->payOutInterval);
        $systemSetings ->setDepricated(false);
        $systemSetings ->setDatabaseClear($values->databaseClear);
        $systemSetings ->setAnalyticsGarbage($values->analyticsGarbage);
        $systemSetings ->setRelevantFrom(new DateTimeImmutable());

        $this->systemSetingsRepository->save($systemSetings);

        $this->flashMessage('Nastavení systému byly uloženy','alert-success');
        $this->redirect('this');

    }
}