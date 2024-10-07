<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Citys
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer', length: 255)]
    #[ORM\OneToMany(targetEntity: "Users", mappedBy: "cityCode")]
    #[ORM\JoinColumn(name: "city_code", referencedColumnName: "cityCode")]
    private int $cityCode;

    #[ORM\Column(type: 'string', length: 35)]
    private string $cityName;

    #[ORM\Column(type: 'string', length: 22)]
    private string $region;

    #[ORM\Column(type: 'string', length: 35)]
    private string $cityOffice;

    #[ORM\Column(type: 'string', length: 35)]
    private string $country;

    #[ORM\Column(type: 'string', length: 5)]
    private string $countryCode;



    public function getId(): int
    {
        return $this->id;
    }

    public function getCityName(): string
    {
        return $this->cityName;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getCityOffice(): string
    {
        return $this->cityOffice;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getCityCode(): int
    {
        return $this->cityCode;
    }

}