<?php
declare(strict_types=1);

namespace App\Components\Datagrids;

use App\Model\Orm\Repository\PageRepository;
use Ublaboo\DataGrid\DataGrid;

class PagesDatagridFactory extends BaseDatagridFactory
{

    private $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {

        $this->pageRepository = $pageRepository;
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
        $grid->setDataSource($this->pageRepository->findAll());

        $grid->addColumnText('title', 'Název')
            ->setFilterText();
        $grid->addColumnText('link', 'Odkaz')
            ->setFilterText();

        $grid->addColumnText('createdBy', 'Vytvořil')
            ->setRenderer(function ($item) {
                return $item->getAuthor()->getUserName();
            });

        $grid->addAction('edit', '', 'Admin:page', ['id' => 'id'])
            ->setIcon('pencil-alt');
        $grid->addAction('delete', '', 'Admin:deletePage', ['id' => 'id'])
            ->setIcon('trash-alt')
            ->setClass('btn btn-danger');

        return $grid;
    }
}