<?php

namespace App\Components\Datagrids;

use App\Model\Orm\Repository\CollectionsRepository;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

class CollectionsDatagridFactory extends DataGrid
{

    private CollectionsRepository $collectionsRepository;

    public function __construct(CollectionsRepository $collectionsRepository)
    {
        parent::__construct();
        $this->collectionsRepository = $collectionsRepository;
    }

    /**
     * @throws DataGridException
     */
    public function create(): DataGrid
    {
        $data = $this->collectionsRepository->findAll();
        $grid = new DataGrid();
        $grid->setTranslator((new SimpleTranslator([
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
        ])));
        $grid->setDataSource($data);
        $grid->addColumnText('id', 'Id');
        $grid->addColumnText('Azyl','Azyl')
            ->setRenderer(function ($item) {
                return $item->getAzyl()->getAzylName();
            });
        $grid->addColumnText('User','Uživatel')
            ->setRenderer(function ($item) {
                return $item->getUser()->getUserName();
            });
        $grid->addColumnDateTime('createdAt', 'Vytvořeno');
        $grid->addColumnDateTime('startAt', 'Začátek');
        $grid->addColumnDateTime('endingAt', 'Končí');
        $grid->addColumnDateTime('extendTo', 'Prodloužení');
        $grid->addColumnText('collectionKey','VS');
        $grid->addColumnText('collectionName','Název')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('collectionDescription','Popis')
            ->setRenderer(function ($item) {
                return ($item->getCollectionDescription());
            })
            ->setTemplateEscaping(false);
        $grid->addColumnText('minimalAmount','Nejmenší příhoz');
        $grid->addColumnText('resultAmount','Cílovka');
        $grid->addColumnText('extendedAmount','Pr. Cílovka');
        $grid->addColumnText('currency','Měna');
        $grid->addColumnStatus('extend','Ext')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
            ->addOption(true, 'Ano')
            ->setClass('btn-sm btn-success')
            ->setIcon('fa fa-check')
            ->endOption()
            ->addOption(false,'Ne')
            ->setClass('btn-sm btn-warning')
            ->setIcon('fa fa-times')
            ->endOption()
            ->onChange[] = [$this, 'changeCollectionActive'];
        $grid->addColumnStatus('isActive','Aktivní')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
            ->addOption(true, 'Aktivní')
            ->setClass('btn-sm btn-success')
            ->setIcon('fa fa-check')
            ->endOption()
            ->addOption(false,'Neaktivní')
            ->setClass('btn-sm btn-warning')
            ->setIcon('fa fa-times')
            ->endOption()
            ->onChange[] = [$this, 'changeCollectionActive'];
        $grid->addColumnStatus('approved','Stav')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
            ->addOption(true, 'Schváleno')
            ->setClass('btn-sm btn-success')
            ->setIcon('fa fa-check')
            ->endOption()
            ->addOption(false,'Neschváleno')
            ->setClass('btn-sm btn-warning')
            ->setIcon('fa fa-times')
            ->endOption()
            ->onChange[] = [$this, 'changeCollectionStatus'];

        return $grid;
    }


}