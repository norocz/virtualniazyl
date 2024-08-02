<?php

declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Citys;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $entityClass = Citys::class)
    {
        parent::__construct($em, $em->getClassMetadata($entityClass));
    }
    public function createCity(int $id, int $cityCode, string $cityName, string $region, string $cityOffice, string $country)
    {
        $city = new Citys($id, $cityCode, $cityName, $region, $cityOffice, $country);
        $this->getEntityManager()->persist($city);
        $this->getEntityManager()->flush();
    }

    public function updataCity(int $id, int $cityCode, string $cityName, string $region, string $cityOffice, string $country)
    {
        $city = $this->findCityById($id);
        $city->setCityCode($cityCode);
        $city->setCityName($cityName);
        $city->setRegion($region);
        $city->setCityOffice($cityOffice);
        $city->setCountry($country);
        $this->getEntityManager()->flush();
    }
    public function findAll()
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ->getResult();
    }

    public function findCityById(int $id)
    {
        return $this->createQueryBuilder('c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCityByName(string $name)
    {
        return $this->createQueryBuilder('c')
            ->where('c.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCityByRegion(string $region)
    {
        return $this->createQueryBuilder('c')
            ->where('c.region = :region')
            ->setParameter('region', $region)
            ->getQuery()
            ->getResult();
    }

    public function findCityByCityOffice(string $cityOffice)
    {
        return $this->createQueryBuilder('c')
            ->where('c.cityOffice = :cityOffice')
            ->setParameter('cityOffice', $cityOffice)
            ->getQuery()
            ->getResult();
    }

    public function findCityByCountry(string $country)
    {
        return $this->createQueryBuilder('c')
            ->where('c.country = :country')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();
    }

    //TODO: DOplnit načtení zemí podle dat v DB - nejdřív je potřeba doplnit data do DB přidat tam slovensko a slovenské okresy.
    public function fetchCountries()
    {
        return $this->createQueryBuilder('c')
            ->select('c.country')
            ->groupBy('c.country, c.countryCode')
            ->getQuery()
            ->getResult();
    }

    public function findRegionByCountry(mixed $country)
    {
        return $this->createQueryBuilder('c')
            ->select('c.region')
            ->where('c.country = :country')
            ->setParameter('country', $country)
            ->groupBy('c.region')
            ->getQuery()
            ->getResult();
    }


}