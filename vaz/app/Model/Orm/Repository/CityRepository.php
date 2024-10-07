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

    public function updateCity(int $id, int $cityCode, string $cityName, string $region, string $cityOffice, string $country)
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

    public function findCityByRegion(string $region):array
    {
        $pole = $this->createQueryBuilder('c')
            ->where('c.region = :region')
            ->setParameter('region', $region)
            ->orderBy('c.cityName', 'ASC')
            ->getQuery()
            ->getResult();
        foreach ($pole as $city) {
        $return[$city->getId()] = $city->getCityName();
        }
        return $return;

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
    public function fetchCountriesOld():array
    {
        return $this->createQueryBuilder('c')
            ->select('c.*')
            ->groupBy('c.country, c.country')
            ->getQuery()
            ->getResult();
    }

    public function fetchCountries():array
    {
        $pole = $this->createQueryBuilder('c')
            ->select('c.country')
            ->groupBy('c.country')
            ->orderBy('c.country', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($pole['0'] as $country) {

            $kole[$country] = $country;
        }


        return $kole;

    }

    public function findRegionByCountry(mixed $country):array
    {
        $pole = $this->createQueryBuilder('c')
            ->select('c.id, c.region')  // Vyber ID a název regionu
            ->where('c.country = :country')
            ->setParameter('country', $country)
            ->groupBy('c.id, c.region') // Skupinování podle ID a názvu regionu
            ->orderBy('c.region', 'ASC')
            ->getQuery()
            ->getResult();


        $pole = array_column($pole, 'region', 'region');
        return $pole;
    }


}