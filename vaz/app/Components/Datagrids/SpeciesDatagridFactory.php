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

        $this->speciesRepository = $speciesRepository;
        $this->sexTypeEnum = $sexTypeEnum;
    }

    public function create(): DataGrid
    {
        $grid = new DataGrid;
        $grid->setTranslator(new \Ublaboo\DataGrid\Localization\SimpleTranslator([
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
        ]));
        $grid->setRememberState(false);
        $grid->setDataSource($this->speciesRepository->findAll());

        $grid->addColumnText('name', 'Druh')
            ->setFilterText();
        $grid->addColumnText('description', 'Popis')
            ->setFilterText();
        $grid->addColumnText('tags','Tagy')
            ->setFilterText();
        $grid->addColumnText('sex', 'Pohlaví')
        ->setFilterSelect($this->sexTypeEnum->getSexTypesForm());

        $grid->addAction('edit', '', 'Admin:species', ['id' => 'id'])
            ->setIcon('pencil-alt');

        return $grid;
    }

}