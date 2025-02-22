<?php
namespace App\Components;

use App\Model\Orm\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Selectt\ResultEntity;
use Selectt\SelecttDataSource;

class CityDataSource implements SelecttDataSource
{

    public function __construct(private CityRepository $cityRepository)
    {

        $this->cityRepository = $cityRepository;
    }

    public function searchTerm(?string $query, int $limit, int $offset): array
    {
        return $this->cityRepository->findByAutocomplete($query, $limit, $offset);

    }

    public function searchTermCount(?string $query): int
    {
        return $this->cityRepository->countForAutocomplete($query);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByKey($key): ?ResultEntity
    {
        $city = $this->cityRepository->findCityById($key);

        return $city ? new ResultEntity($city->getId(), $city->getName()) : null;
    }

    public function findByKeys(array $keys): array
    {
        return $this->cityRepository->findBy(['id' => $keys]);

    }
}