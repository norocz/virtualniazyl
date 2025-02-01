<?php
declare(strict_types=1);

namespace App\Components\Datagrids;

use App\Model\Orm\Enums\RoleTypeEnum;
use App\Model\Orm\Repository\UsersRepository;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\Exception\DataGridColumnStatusException;
use Ublaboo\DataGrid\InlineEdit\InlineEdit;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

class UsersDatagridFactory extends Datagrid
{
private UsersRepository $usersRepository;
private RoleTypeEnum $roleTypeEnum;

    public function __construct(UsersRepository $usersRepository)
    {
        parent::__construct();
        $this->usersRepository = $usersRepository;
    }

    protected ?Presenter $presenter = null;
    public function setPresenter(Presenter $presenter): void
    {
        $this->presenter = $presenter;
    }

    /**
     * @throws DataGridException
     * @throws DataGridColumnStatusException
     */
    public function create(): void
    {
        $translator = new SimpleTranslator([
            'ublaboo_datagrid.no_item_found_reset' => 'Žádné položky nenalezeny. Filtr můžete vynulovat',
            'ublaboo_datagrid.no_item_found' => 'Žádné položky nenalezeny.',
            'ublaboo_datagrid.here' => 'zde',
            'ublaboo_datagrid.items' => 'Položky',
            'ublaboo_datagrid.all' => 'všechny',
            'ublaboo_datagrid.from' => 'z',
            'ublaboo_datagrid.reset_filter' => 'Resetovat filtr',
            'ublaboo_datagrid.group_actions' => 'Hromadné akce',
            'ublaboo_datagrid.show_all_columns' => 'Zobrazit všechny sloupce',
            'ublaboo_datagrid.hide_column' => 'Skrýt sloupec',
            'ublaboo_datagrid.action' => 'Akce',
            'ublaboo_datagrid.previous' => 'Předchozí',
            'ublaboo_datagrid.next' => 'Další',
            'ublaboo_datagrid.choose' => 'Vyberte',
            'ublaboo_datagrid.execute' => 'Provést',
            'ublaboo_datagrid.Change' => 'Změnit',


            'Name' => 'Jméno',
            'Inserted' => 'Vloženo'
        ]);
        $this->setTranslator($translator);


        $this->setRememberState(false);
        $this->setDataSource($this->usersRepository->findBy(['role' => [RoleTypeEnum::ROLE_AZYL, RoleTypeEnum::ROLE_USER, RoleTypeEnum::ROLE_ADOPTER, RoleTypeEnum::ROLE_GUEST, RoleTypeEnum::ROLE_OWNER, RoleTypeEnum::ROLE_REVIEWER]]));

        $this->addColumnText('id', 'ID')
            ->setDefaultHide(true)
            ->setSortable();
        $this->addColumnText('userName', 'Uživatelské jméno')
            ->setSortable()
            ->setFilterText();
        $this->addColumnText('email', 'Email')
            ->setSortable()
            ->setFilterText();

        $this->addColumnStatus('verified', 'Ověřený')
              ->setTemplate(__DIR__ .'/templates/column_status.latte')
            ->setRenderer(function ($item) {return $item->getVerified() ? 'Ano' : 'Ne';})
            ->setSortable()
            ->addOption(true, 'Ano')
                  ->setClass('btn-sm btn-warning')
                  ->setIcon('fa fa-warning')
              ->endOption()
              ->addOption(false,'Ne')
                  ->setClass('btn-sm btn-success')
                  ->setIcon('fa fa-times')
                  ->setConfirmation(new StringConfirmation('Chcete nastavit stav ne?'))
              ->endOption()
            ->onChange[] = [$this, 'updateVerifiedState'];

        $this->addColumnStatus('deleted', 'Smazán')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
            ->addOption(true, 'Ano')
            ->setClass('btn-sm btn-warning')
            ->setIcon('fa fa-warning')
            ->endOption()
            ->addOption(false,'Ne')
            ->setClass('btn-sm btn-success')
            ->setIcon('fa fa-times')
            ->setConfirmation(new StringConfirmation('Chcete nastavit stav ne?'))
            ->endOption()
            ->onChange[] = [$this, 'updateDletedState'];

        $this->addColumnStatus('baned' , 'Ban?')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
            ->setRenderer(function ($item) {return $item->isBaned() ? 'Ano' : 'Ne';})
            ->setSortable()
            ->setCaret(true)
            ->addOption(true, 'Ano')
            ->setClass('btn-sm btn-warning')
            ->setIcon('fa fa-warning')
            ->endOption()
            ->addOption(false,'Ne')
            ->setClass('btn-sm btn-success')
            ->setIcon('fa fa-times')
            ->setConfirmation(new StringConfirmation('Chcete nastavit stav ne?'))
            ->endOption()
            ->onChange[] = [$this, 'updateBanState'];
        $this->addColumnStatus('mailverified', 'Email')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
            ->setRenderer(function ($item) {return $item->isMeilVerified() ? 'Ano' : 'Ne';})
            ->setSortable()
            ->setCaret(true)
            ->addOption(true, 'Ověřen')
            ->setClass('btn-sm btn-warning')
            ->setIcon('fa fa-warning')
            ->endOption()
            ->addOption(false,'Neověřen')
            ->setClass('btn-sm btn-success')
            ->setIcon('fa fa-times')
            ->setConfirmation(new StringConfirmation('Chcete nastavit stav ne?'))
            ->endOption()
            ->onChange[] = [$this, 'updateMailVerifiedState'];
        $this->addColumnStatus('phoneverified', 'Telefon ověřen')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
           // ->setRenderer(function ($item) {return $item->isPhoneVerified() ? 'Ano' : 'Ne';})
           // ->setSortable()
            ->setCaret(false)
            ->addOption(true, 'Ano')
                ->setClass('btn-sm btn-warning')
                ->setIcon('fa fa-warning')
                    ->endOption()
            ->addOption(false,'Ne')
                ->setClass('btn-sm btn-success')
                ->setIcon('fa fa-times')
                ->setConfirmation(new StringConfirmation('Chcete nastavit stav ne?'))
                    ->endOption()
            ->onChange[] = [$this, 'updatePhoneVerifiedState'];

        $this->addColumnDateTime('created_at', 'Registrace')
            ->setFormat(format: 'd.m.Y H:i:s')
            ->setSortable()
            ->setFilterDate();
        $this->addColumnDateTime('updated_at', 'Aktualizace')
            ->setFormat(format: 'd.m.Y H:i:s')
            ->setSortable()
            ->setFilterDate();

        $this->addAction('edit', '', 'editUser!')
            ->setIcon('pencil-alt')
            ->setClass('btn btn-sm btn-primary');
        $this->addAction('delete', '', 'deleteUser!')
            ->setIcon('trash')
            ->setClass('btn btn-sm btn-danger')
            ->addAttributes(['data-confirm' => 'Skutečně chcete smazat uživatele?']);

    }

    public function updateVerifiedState($id, string $newValue): void
    {
        $user = $this->usersRepository->findOneBy(['id' => intval($id)]);
        $user -> setVerified(boolval($newValue));
        $this->usersRepository->save($user);
        $this->presenter->flashMessage('Změna stavu verifikace provedena', 'alert-success');
        if($this->presenter->isAjax()) {
            $this->presenter->redrawControl('datagrid');
            $this->presenter->redrawControl('flash');

        }
        else
        {
            $this->presenter->redirect('this');
        }
    }

    public function updateBanState($id, string $newValue): void
    {
        $user = $this->usersRepository->findOneBy(['id' => intval($id)]);
        $user -> setBaned(boolval($newValue));
        $this->usersRepository->save($user);
        $this->presenter->flashMessage('Změna stavu banu provedena', 'alert-success');
        if($this->presenter->isAjax()) {
            $this->presenter->redrawControl('datagrid');
            $this->presenter->redrawControl('flash');

        }
        else
        {
            $this->presenter->redirect('this');
        }


    }

    public function updatePhoneVerifiedState($id, string $newValue): void
    {
        $user = $this->usersRepository->findOneBy(['id' => intval($id)]);
        $user -> setPhoneVerified(boolval($newValue));
        $this->usersRepository->save($user);
        $this->presenter->flashMessage('Změna verifikace telefonu provedena', 'alert-success');
        if($this->presenter->isAjax()) {
            $this->presenter->redrawControl('datagrid');
            $this->presenter->redrawControl('flash');

        }
        else
        {
            $this->presenter->redirect('this');
        }


    }

    public function updateMailVerifiedState($id, string $newValue): void
    {
        $user = $this->usersRepository->findOneBy(['id' => intval($id)]);
        $user -> setMailverified(boolval($newValue));
        $this->usersRepository->save($user);
        $this->presenter->flashMessage('Změna Verifikace emailu provedena', 'alert-success');
        if($this->presenter->isAjax()) {
            $this->presenter->redrawControl('datagrid');
            $this->presenter->redrawControl('flash');

        }
        else
        {
            $this->presenter->redirect('this');
        }


    }

    public function updateDletedState($id, string $newValue): void
    {
        $user = $this->usersRepository->findOneBy(['id' => intval($id)]);
        $user -> setDeleted(boolval($newValue));
        $this->usersRepository->save($user);
        $this->presenter->flashMessage('Změna stavu smazání provedena', 'alert-success');
        if($this->presenter->isAjax()) {
            $this->presenter->redrawControl('datagrid');
            $this->presenter->redrawControl('flash');

        }
        else
        {
            $this->presenter->redirect('this');
        }


    }


}