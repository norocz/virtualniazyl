<?php
declare(strict_types=1);

namespace App\Presenters;


use App\Model\Orm\Entity\Citys;
use App\Model\Orm\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;

class JsonPresenter extends Presenter
{

    private CityRepository $cityRepository;
    private EntityManagerInterface $entityManager;


    public function __construct(CityRepository $cityRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->cityRepository = $cityRepository;
        $this->entityManager = $entityManager;

    }
    public function startup(): void
    {
        parent::startup();

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

    public function actionSelect2(string $search = '', int $page = 1): void
    {
        $limit = 15;
        $offset = ($page - 1) * $limit;

        // Pokud uživatel zadal méně než 2 znaky, vrátíme prázdný výsledek
        if (mb_strlen($search) < 2) {
            $this->sendJson([
                'results' => [],
                'pagination' => ['more' => false]
            ]);
        }

        // 1️⃣ Získání měst z databáze
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Citys::class, 'c')
            ->where('c.cityName LIKE :query')
            ->setParameter('query', "%$search%")
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $cities = $qb->getQuery()->getResult();

        // 2️⃣ Seskupení podle regionů pro Select2
        $groupedCities = [];
        foreach ($cities as $city) {
            $region = $city->getRegion();

            if (!isset($groupedCities[$region])) {
                $groupedCities[$region] = [
                    'text' => $region,
                    'children' => []
                ];
            }

            $groupedCities[$region]['children'][] = [
                'id' => $city->getId(),
                'text' => $city->getCityName()
            ];
        }

        // 3️⃣ Počet celkových výsledků (pro stránkování)
        $totalCount = $this->entityManager->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(Citys::class, 'c')
            ->where('c.cityName LIKE :query')
            ->setParameter('query', "%$search%")
            ->getQuery()
            ->getSingleScalarResult();

        $pagination = [
            'more' => ($offset + $limit) < $totalCount
        ];

        // 4️⃣ Odeslání JSON odpovědi pro Select2
        $this->sendJson([
            'results' => array_values($groupedCities),
            'pagination' => $pagination
        ]);
    }
}

