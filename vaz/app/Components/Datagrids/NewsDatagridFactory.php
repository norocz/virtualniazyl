<?php
declare(strict_types=1);

namespace App\Components\Datagrids;

use App\Model\Orm\Entity\News;
use App\Model\Orm\Repository\NewsRepository;
use Ublaboo\DataGrid\DataGrid;

class NewsDatagridFactory extends DataGrid
{
    private NewsRepository $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        parent::__construct();
        $this->newsRepository = $newsRepository;
    }

    public function create($id): DataGrid
    {
        $grid = new DataGrid;
        $grid->setDataSource($this->newsRepository->findAllVisibleUser($id));
        $grid->addColumnText('id', 'ID')
            ->setSortable()
            ->setDefaultHide()
            ->setFilterText();
        $grid->addColumnText('title', 'Title')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('content', 'Content')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnDateTime('createdAt', 'Created at')
            ->setDefaultHide()
            ->setSortable()
            ->setFilterDate();
        $grid->addColumnDateTime('updatedAt', 'Updated at')
            ->setDefaultHide()
            ->setSortable()
            ->setFilterDate();
        $grid->addColumnDateTime('visibleFrom', 'Visible from')
            ->setSortable()
            ->setFilterDate();
        $grid->addColumnStatus('deleted', 'Deleted')

            ->setRenderer(function ($item) {
                return $item->getDeleted() ? 'Ano' : 'Ne';
            })
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('global', 'Global')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('important', 'Important')
            ->setSortable()
            ->setFilterText();
        $grid->addAction('edit', '', 'edit')
            ->setIcon('pencil-alt')
            ->setClass('btn btn-sm btn-primary');
        $grid->addAction('delete', '', 'delete')
            ->setIcon('trash')
            ->setClass('btn btn-sm btn-danger')
            ->addAttributes(['data-confirm' => 'Skutečně chcete smazat novinku?']);

        return $grid;
    }
}