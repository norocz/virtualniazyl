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
    public function startup(): void
    {
        parent::startup();
        if ($this->isAjax()) {
            $this->checkRequirements(null); // Zruší povinné přihlášení pro AJAX
        }
    }



    public function actionCity($id): void
    {
        $city = $this->cityRepository->findCityByRegionArray($id);
        $this->sendJson($city);
    }

    public function actionStates(): void
    {
        $states = $this->cityRepository->fetchStates();
        $this->sendJson($states);
    }

    public function actionRegion($id): void
    {
        $regions = $this->cityRepository->findRegionByCountry($id);
        $this->sendJson($regions);

    }
}

