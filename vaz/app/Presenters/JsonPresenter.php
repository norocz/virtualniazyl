<?php
declare(strict_types=1);

namespace App\Presenters;


use App\Model\Orm\Entity\Citys;
use App\Model\Orm\Repository\CityRepository;
use Nette\Application\UI\Presenter;

class JsonPresenter extends Presenter
{


    private CityRepository $cityRepository;

    public function __construct(CityRepository $cityRepository)
    {
        parent::__construct();
        $this->cityRepository = $cityRepository;

    }

    public function actionCities($region): void
    {
        $cities = $this->cityRepository->findCityByRegion($region);
        $this->sendJson($cities);
    }

    public function actionStates(): void
    {
        $states = $this->cityRepository->fetchStates();
        $this->sendJson($states);
    }

    public function actionRegions(): void
    {
        $regions = $this->cityRepository->fetchRegions();
        $this->sendJson($regions);
    }
}

