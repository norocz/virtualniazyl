<?php
declare(strict_types=1);

namespace App\Presenters;


use App\Model\Orm\Repository\AnalyticsRepository;
use App\Model\Orm\Repository\AzylRepository;
use App\Model\Orm\Repository\FirewallLogsRepository;
use App\Services\IpInfoService;
use Contributte\Application\UI\BasePresenter;
use Nette\Application\UI\Form;
use App\Forms\setAzylFormFactory;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Security\SimpleIdentity;
use Nette\Utils\Paginator;

class SuperAdminPresenter extends BasePresenter
{

    private setAzylFormFactory $setAzylFormFactory;
    private AzylRepository $azylRepository;
    private FirewallLogsRepository $firewallLogsRepository;
    private analyticsRepository $analyticsRepository;
    private ipInfoService $ipInfoService;
    public function __construct(setAzylFormFactory $setAzylFormFactory,
                                azylRepository $azylRepository,
                                firewallLogsRepository $firewallLogsRepository,
                                analyticsRepository $analyticsRepository,
                                ipInfoService $ipInfoService)
    {
        parent::__construct();
        $this->setAzylFormFactory = $setAzylFormFactory;
        $this->azylRepository = $azylRepository;
        $this->firewallLogsRepository = $firewallLogsRepository;
        $this->analyticsRepository = $analyticsRepository;
        $this->ipInfoService = $ipInfoService;

    }

    public function startup():void
    {
        parent::startup();

            if (!$this->getPresenter()->user->isLoggedIn() && !$this->getPresenter()->getUser()->isInRole('superadmin')) {
            $this->getPresenter()->redirect('Home:default');
        }


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

    public function azylSetFormSuccessed(Form $form, \stdClass $values) : void
    {

        $azyl = $this->azylRepository->findById($values->azyl);
        $identity = $this->getUser()->getIdentity();
        if ($identity) {

            $newData = $identity->getData();
            $newData['Azyl'] = $azyl;
            $newIdentity = new SimpleIdentity($identity->getId(),$identity->getRoles(),$newData);
            $this->user->login($newIdentity);

        }

    }
}