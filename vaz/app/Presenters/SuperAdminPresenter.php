<?php
declare(strict_types=1);

namespace App\Presenters;


use App\Model\Orm\Repository\AzylRepository;
use Contributte\Application\UI\BasePresenter;
use Nette\Application\UI\Form;
use App\Forms\setAzylFormFactory;
use Nette\Security\User;
use Nette\Security\SimpleIdentity;

class SuperAdminPresenter extends BasePresenter
{

    private setAzylFormFactory $setAzylFormFactory;
    private AzylRepository $azylRepository;
    public function __construct(setAzylFormFactory $setAzylFormFactory, azylRepository $azylRepository)
    {
        $this->setAzylFormFactory = $setAzylFormFactory;
        $this->azylRepository = $azylRepository;
        parent::__construct();
    }

    public function startup():void
    {
        parent::startup();

            if (!$this->getPresenter()->user->isLoggedIn() && !$this->getPresenter()->getUser()->isInRole('superadmin')) {
            $this->getPresenter()->redirect('SuperAdmin:SignIn');
        }


    }
    public function renderDefault(): void
    {
        $this->template->title = 'Admin';
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
    // Actions

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