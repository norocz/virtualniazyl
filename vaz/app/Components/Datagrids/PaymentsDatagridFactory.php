<?php

namespace App\Components\Datagrids;

use App\Model\Orm\Repository\PaymentsRepository;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Localization\SimpleTranslator;

class PaymentsDatagridFactory
{
    private PaymentsRepository $paymentsRepository;

    public function __construct(PaymentsRepository $paymentsRepository)
    {
        $this->paymentsRepository = $paymentsRepository;
    }


    public function create(): Datagrid
    {
        $datagrid = new Datagrid();
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
        $datagrid->setTranslator($translator);


        $datagrid->setRememberState(false);
        $datagrid->setDataSource($this->paymentsRepository->findAll());

        $datagrid->addColumnNumber('id','ID');
        $datagrid->addColumnDateTime('created_at','Vytvořeno')
            ->setFormat(format: 'd.m.Y H:i:s')
            ->setSortable()
            ->setFilterDate();
        $datagrid->addColumnDateTime('payed_at','Zaplaceno')
            ->setFormat(format: 'd.m.Y H:i:s')
            ->setSortable()
            ->setFilterDate();

        $datagrid->addColumnText('$variable_symbol','Var. S.');


        return $datagrid;

    }

}