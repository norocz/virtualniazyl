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