<?php
declare(strict_types=1);

namespace App\Presenters;


use Contributte\Application\UI\BasePresenter;

class SuperAdminPresenter extends BasePresenter
{

    public function __construct()
    {

        parent::__construct();
    }

    public function startup():void
    {
        parent::startup();

        bdump ($this->getPresenter()->getUser()->getRoles());
        if (!$this->getPresenter()->user->isLoggedIn() && !$this->getPresenter()->getUser()->isInRole('superadmin')) {
            $this->getPresenter()->redirect('SuperAdmin:SignIn');
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
}