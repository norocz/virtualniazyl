<?php

declare(strict_types=1);

namespace App\Components\Datagrids;

use App\Model\Orm\Enums\SexTypeEnum;
use App\Repository\SpeciesRepository;
use Ublaboo\DataGrid\DataGrid;

class SpeciesDatagridFactory extends BaseDatagridFactory
{
    private SpeciesRepository $speciesRepository;
    private SexTypeEnum $sexTypeEnum;

    public function __construct(SpeciesRepository $speciesRepository, SexTypeEnum $sexTypeEnum)
    {
        parent::__construct();
        $this->speciesRepository = $speciesRepository;
        $this->sexTypeEnum = $sexTypeEnum;
    }

    public function create(): DataGrid
    {
        $grid = new DataGrid;
        $grid->setRememberState(false);
        $grid->setDataSource($this->speciesRepository->findAll());

        $grid->addColumnText('name', 'Druh')
            ->setFilterText();
        $grid->addColumnText('description', 'Popis')
            ->setFilterText();
        $grid->addColumnText('sex', 'Pohlaví')
        ->setFilterSelect($this->sexTypeEnum->getSexTypesForm());

        return $grid;
    }

}