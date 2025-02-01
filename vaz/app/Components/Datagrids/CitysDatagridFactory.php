<?php
declare(strict_types=1);

namespace App\Components\Datagrids;

use App\Model\Orm\Repository\CityRepository;
use Ublaboo\DataGrid\DataGrid;

class CitysDatagridFactory extends BaseDatagridFactory
{
    private CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
       $this->cityRepository = $cityRepository;
    }
    public function create(): DataGrid
    {
        $grid = new DataGrid;
        $grid->setRememberState(false);
        $grid->setDataSource($this->cityRepository->findAll());

        $grid->addColumnText('country', 'Země');
        $grid->addColumnText('region', 'Okres')
            ->setFilterText();
        $grid->addColumnText('cityOffice', 'Správní obec')
            ->setFilterText();
        $grid->addColumnText('cityName', 'Obec')
            ->setFilterText();





        return $grid;
    }
}