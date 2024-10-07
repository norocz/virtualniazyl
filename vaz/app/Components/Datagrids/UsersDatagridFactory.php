<?php
declare(strict_types=1);

namespace App\Components\Datagrids;

use App\Model\Orm\Enums\RoleTypeEnum;
use App\Model\Orm\Repository\UsersRepository;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\InlineEdit\InlineEdit;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class UsersDatagridFactory extends DataGrid
{
private UsersRepository $usersRepository;
private RoleTypeEnum $roleTypeEnum;

    public function __construct(UsersRepository $usersRepository, RoleTypeEnum $roleTypeEnum)
    {
        parent::__construct();
        $this->usersRepository = $usersRepository;
        $this->roleTypeEnum = $roleTypeEnum;
    }

    /**
     * @throws DataGridException
     */
    public function create(): DataGrid
    {
        $role = $this->roleTypeEnum->getRoles();
        $admins = $this->usersRepository->findBy(['role' => RoleTypeEnum::ROLE_ADMIN]);

        $grid = new DataGrid;
        $grid->setRememberState(false);
        $grid->setDataSource($this->usersRepository->findBy(['role' => RoleTypeEnum::ROLE_USER]));
     //  $grid->setDataSource($this->usersRepository->findAll()); //tohle by se mělo měnit podle toho co filtrujeme
        $grid->addColumnText('id', 'ID')
            ->setDefaultHide(true)
            ->setSortable();
        $grid->addColumnText('userName', 'Uživatelské jméno')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('email', 'Email')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnStatus('role', 'Role')
            ->setSortable()
            ->setCaret(true)
            ->setOptions( options: $role)
            ->onChange[] = function ($id, $value) {$user = $this->usersRepository->getUserById($id); $user->setRole($value); $this->usersRepository->addUser($user); dump($id,$value);
            die;};
          $grid->addColumnStatus('verified', 'Ověřený')
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
            ->onChange[] = function ($id, $value) {$user = $this->usersRepository->getUserById($id); $user->setVerified($value); $this->usersRepository->addUser($user);};
        $grid->addColumnStatus('deleted', 'Smazán')
            ->addOption(true, 'Ano')
            ->setClass('btn-sm btn-warning')
            ->setIcon('fa fa-warning')
            ->endOption()
            ->addOption(false,'Ne')
            ->setClass('btn-sm btn-success')
            ->setIcon('fa fa-times')
            ->setConfirmation(new StringConfirmation('Chcete nastavit stav ne?'))
            ->endOption()
            ->onChange[] = function ($id, $value) {$user = $this->usersRepository->getUserById($id); $user->setDeleted($value); $this->usersRepository->addUser($user);};
        $grid->addColumnStatus('baned' , 'Ban?')
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
            ->onChange[] = function ($id, $value) {$user = $this->usersRepository->getUserById($id); $user->setBaned($value); $this->usersRepository->addUser($user);};
        $grid->addColumnStatus('mailverified', 'Email')
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
            ->onChange[] = function ($id, $value) {$user = $this->usersRepository->getUserById($id); $user->setMailverified($value); $this->usersRepository->addUser($user);};
        $grid->addColumnStatus('phoneverified', 'Telefon ověřen')
            ->setRenderer(function ($item) {return $item->isPhoneVerified() ? 'Ano' : 'Ne';})
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
            ->onChange[] = function ($id, $value) {$user = $this->usersRepository->getUserById($id); $user->setPhoneVerified($value); $this->usersRepository->addUser($user);};

        $grid->addColumnDateTime('created_at', 'Registrace')
            ->setFormat(format: 'd.m.Y H:i:s')
            ->setSortable()
            ->setFilterDate();
        $grid->addColumnDateTime('updated_at', 'Aktualizace')
            ->setFormat(format: 'd.m.Y H:i:s')
            ->setSortable()
            ->setFilterDate();

        $grid->addAction('edit', '', 'edit')
            ->setIcon('pencil-alt')
            ->setClass('btn btn-sm btn-primary');
        $grid->addAction('delete', '', 'delete')
            ->setIcon('trash')
            ->setClass('btn btn-sm btn-danger')
            ->addAttributes(['data-confirm' => 'Skutečně chcete smazat uživatele?']);

        return $grid;
    }
}