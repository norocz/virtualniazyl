<?php
declare(strict_types=1);

namespace App\Components\Datagrids;

use App\Model\Orm\Entity\News;
use App\Model\Orm\Repository\NewsRepository;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridColumnStatusException;
use Ublaboo\DataGrid\Exception\DataGridException;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

class NewsDatagridFactory extends BaseDatagridFactory
{

    private NewsRepository $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        parent::__construct();
        $this->newsRepository = $newsRepository;
    }


    public function setPresenter(Presenter $presenter): void
    {
        $this->presenter = $presenter;
    }

    /**
     * @throws DataGridColumnStatusException
     * @throws DataGridException
     */
    public function create(): DataGrid
    {
        $grid = new DataGrid;

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
        $grid->setTranslator($translator);
        $grid->setDataSource($this->newsRepository->findAllVisibleUser($this->presenter->getUser()->getId()));
        $grid->addColumnText('id', 'ID')
            ->setSortable()
            ->setDefaultHide()
            ->setFilterText();
        $grid->addColumnText('title', 'Titulek')
            ->setSortable()
            ->setFilterText();
        $grid->addColumnText('content', 'Obsah')
            ->setRenderer(function ($item) {
                return ($item->getContent());
            })
            ->setSortable()
            ->setTemplateEscaping(FALSE)
            ->setFilterText();
        $grid->addColumnDateTime('createdAt', 'Vytvořeno')
            ->setDefaultHide()
            ->setFormat('Y-m-d H:i:s')
            ->setSortable()
            ->setFilterDate();
        $grid->addColumnDateTime('updatedAt', 'Aktualizováno')
            ->setDefaultHide()
            ->setFormat('Y-m-d H:i:s')
            ->setSortable()
            ->setFilterDate();
        $grid->addColumnDateTime('visibleFrom', 'Zveřejněno')
            ->setFormat('Y-m-d H:i:s')
            ->setSortable()
            ->setFilterDate();
/*
        $grid->addColumnStatus('deleted', 'Deleted')
            ->addOption(true, 'Smazáno')
            ->setClass('btn-sm btn-warning')
            ->setIcon('fa fa-warning')
            ->endOption()
            ->addOption(false,'Nesmazáno')
            ->setClass('btn-sm btn-success')
            ->setIcon('fa fa-times')
            ->setConfirmation(new StringConfirmation('Chcete obnovit novinku?'))
            ->endOption()
            ->onChange[] = [$this, 'deleteNewsChange'];
*/

        $grid->addColumnStatus('global', 'Globálnost')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
            ->addOption(true, 'Globální')
                ->setClass('btn-sm btn-success')
                ->setIcon('fa fa-check')
                ->setConfirmation(new StringConfirmation('Globální položka je vidět na hlavní stránce webu! Pozor na to!'))
                ->endOption()
            ->addOption(false,'Soukromá')
                ->setClass('btn-sm btn-warning')
                ->setIcon('fa fa-times')
                ->endOption()
            ->onChange[] = [$this, 'updateGlobalState'];

        $grid->addColumnStatus('important', 'Důležitost')
            ->setTemplate(__DIR__ .'/templates/column_status.latte')
            ->addOption(true, 'Důležitá')
                ->setClass('btn-sm btn-warning')
                ->setIcon('fa fa-check')
                ->endOption()
            ->addOption(false,'Běžná')
                ->setClass('btn-sm btn-primary')
                ->setIcon('fa fa-times')
                ->setConfirmation(new StringConfirmation('Skutečně je položka důležitá?'))
                ->endOption()
            ->onChange[] = [$this, 'importantNewsChange'];

        $grid->addAction('edit', '', 'news', ['id' => 'id'])
            ->setIcon('pencil-alt')
            ->setClass('btn btn-sm btn-primary');

        $grid->addAction('delete', '', 'newsDelete!')
            ->setIcon('trash')
            ->setClass('btn btn-sm btn-danger')
            ->setConfirmation(new CallbackConfirmation(
                                function($item) {return 'Opravdu chcete smazat novinku'.$item->getTitle().'??';}
            ));
        return $grid;
    }

    public function updateGlobalState($id, string $newValue): void
    {
        $news = $this->newsRepository->findOneBy(['id' => intval($id)])->setGlobal( boolval($newValue));
        $this->newsRepository->save($news);
        $this->presenter->flashMessage('Změna globálnosti provedena', 'alert-success');
        if ($this->presenter->isAjax()) {
            $this->presenter->redrawControl('datagrid');
            $this->presenter->redrawControl('flashdata');
        } else {
            $this->presenter->redirect('this');
        }
    }

    public function importantNewsChange($id, string $newValue): void
    {
       $news = $this->newsRepository->findOneBy(['id' => intval($id)])->setImportant( boolval($newValue));
       $this->newsRepository->save($news);
        $this->presenter->flashMessage('Změna důležitosti provedena', 'alert-success');
        if ($this->presenter->isAjax()) {
            $this->presenter->redrawControl();
        } else {
            $this->presenter->redirect('this');
        }
    }

    private function deleteNewsChange($id, $newValue):void
    {
        $this->newsRepository->findOneBy(['id' => $id])->setDeleted($newValue);
        $this->flashMessage('Změna smazanosti provedena', 'alert-success');
        if ($this->isAjax()) {
            $this->redrawControl('datagrid');
            $this->redrawControl('flashdata');
        } else {
            $this->redirect('this');
        }
    }
}