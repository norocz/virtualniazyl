<?php

namespace App\Components\Datagrids;

use App\Model\Orm\Repository\ContractPartsRepository;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

class ContractPartsDatagridFactory
{
    private ContractPartsRepository $contractPartsRepository;

    public function __construct(ContractPartsRepository $contractPartsRepository)
    {

        $this->contractPartsRepository = $contractPartsRepository;
    }

    /**
     * @throws DataGridException
     */
    public function create(): DataGrid
    {
        $data = $this->contractPartsRepository->fetchAllDatagrid();
        $datagrid = new DataGrid();
        $datagrid->setTranslator((new SimpleTranslator([
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
        $datagrid->setDataSource($data);
        $datagrid->setTranslator(new SimpleTranslator());

        $datagrid->addColumnText('name', 'Název')
                 ->setSortable();
        $datagrid->addColumnDateTime('createdAt','Vytvořeno')
                 ->setSortable();
        $datagrid->addColumnDateTime('closedAt','Platnost');
        $datagrid->addAction('edit', '', 'contractparts', ['id' => 'id'])
            ->setIcon('pencil-alt')
            ->setClass('btn btn-sm btn-primary');

        $datagrid->addAction('delete', '', 'contractPartClose!')
            ->setIcon('trash')
            ->setClass('btn btn-sm btn-danger')
            ->setConfirmation(new CallbackConfirmation(
                function($item) {return 'Opravdu chcete smlouvu zneplatnit?'.$item->getName().'??';}
            ));

        return $datagrid;
    }

    public function handleContractPartClose(int $id): void
    {
        bdump($id);
    }


}
