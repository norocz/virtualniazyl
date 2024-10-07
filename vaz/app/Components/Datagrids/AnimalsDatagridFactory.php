<?php

declare(strict_types=1);

namespace App\Components\Datagrids;

use App\Model\Orm\Repository\AnimalsRepository;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Ublaboo\DataGrid\DataGrid;


class AnimalsDatagridFactory extends DataGrid
{
    private AnimalsRepository $animalsRepository;

    public function __construct(AnimalsRepository $animalsRepository)
    {
        parent::__construct();
        $this->animalsRepository = $animalsRepository;
    }

    public function create(): DataGrid
    {
        $grid = new DataGrid;
        $grid->setRememberState(false);
        $grid->setDataSource($this->animalsRepository->findAll());

       // $grid->addColumnText('species', 'Druh')
         //   ->setFilterText();
        $grid->addColumnText('name', 'Jméno');
        $grid->addColumnText('breed', 'Plemeno')
            ->setFilterText();
        $grid->addColumnDateTime('birthDate', 'Datum narození');
        $grid->addColumnText('age', 'Věk');
        $grid->addColumnText('description', 'Popis')
            ->setFilterText();
        $grid->addColumnText('toAdoption', 'K adopci')
            ->setRenderer(function ($item) {
                return $item->isToAdoption() ? 'Ano' : 'Ne';
            });



      //    $grid->addColumnText('azyl', 'Azyl')
      //       ->setRenderer(function ($item) {return $item->getAzyl()->getAzylName();
      //       }


        $grid->addAction('edit', '', 'Azyl:animal', ['id' => 'id'])
            ->setIcon('pencil-alt')
            ->setTitle('Upravit')
            ->setClass('btn btn-xs btn-primary');
        $grid->addAction('delete', '', 'delete!', ['id' => 'id'])
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setConfirmation(new \Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation('Opravdu chcete smazat záznam?'))
            ->setClass('btn btn-sm btn-danger');


        return $grid;
    }
}