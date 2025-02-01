<?php

declare(strict_types=1);

namespace App\Components\Datagrids;

use App\Model\Orm\Repository\AnimalsRepository;
use Nette\Application\UI\Presenter;

use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridColumnStatusException;
use Ublaboo\DataGrid\Exception\DataGridException;


class AnimalsDatagridFactory extends DataGrid
{
    private AnimalsRepository $animalsRepository;

    public function __construct(AnimalsRepository $animalsRepository)
    {
        parent::__construct();
        $this->animalsRepository = $animalsRepository;
    }

    protected ?Presenter $presenter;

    public function setPresenter(Presenter $presenter): void
    {
        $this->presenter = $presenter;
    }

    /**
     * @throws DataGridColumnStatusException
     * @throws DataGridException
     */
    public function create(): void
    {
        $this->setRememberState(false);
        $this->setDataSource($this->animalsRepository->findAll());

       // $grid->addColumnText('species', 'Druh')
         //   ->setFilterText();
        $this->addColumnText('name', 'Jméno');
        $this->addColumnText('breed', 'Plemeno')
            ->setFilterText();
        $this->addColumnDateTime('birthDate', 'Datum narození');
        $this->addColumnText('age', 'Věk');
        $this->addColumnText('description', 'Popis')
            ->setFilterText();

        $this->addColumnStatus('toAdoption', 'K adopci')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
                ->addOption(true, 'Ano')
                ->setClass('btn-sm btn-success')
                ->setIcon('fa fa-check')
                ->endOption()
                ->addOption(false,'Ne')
                ->setClass('btn-sm btn-warning')
                ->setIcon('fa fa-times')
                ->endOption()
                    ->onChange[] = [$this, 'changeAdoptionStatus'];




      //    $grid->addColumnText('azyl', 'Azyl')
      //       ->setRenderer(function ($item) {return $item->getAzyl()->getAzylName();
      //       }


        $this->addAction('edit', '', 'Azyl:animal', ['id' => 'id'])
            ->setIcon('pencil-alt')
            ->setTitle('Upravit')
            ->setClass('btn btn-xs btn-primary');
        $this->addAction('delete', '', 'delete!', ['id' => 'id'])
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setConfirmation(new \Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation('Opravdu chcete smazat záznam?'))
            ->setClass('btn btn-sm btn-danger');

    }

    public function changeAdoptionStatus($id, string $newValue): void
    {
        $news = $this->animalsRepository->findOneBy(['id' => intval($id)])->setToAdoption(boolval($newValue));
        $this->animalsRepository->flush($news);
        $this->presenter->flashMessage('Změna stavu adopce provedena', 'alert-success');
        if ($this->presenter->isAjax()) {
            $this->presenter->redrawControl('datagrid');
            $this->presenter->redrawControl('flash');
        } else {
            $this->presenter->redirect('this');
        }
    }
}