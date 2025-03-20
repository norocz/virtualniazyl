<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'citys')]
#[ORM\MappedSuperclass]
class Citys
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

   // #[ORM\OneToMany(mappedBy: "city", targetEntity: Users::class)]
    //private ?Collection $cityUsers;

    #[ORM\Column(type: 'string', length: 100)]
    private string $cityName;

    #[ORM\Column(type: 'string', length: 100)]
    private string $region;

    #[ORM\Column(type: 'string', length: 100)]
    private string $cityOffice;

    #[ORM\Column(type: 'string', length: 50)]
    private string $country;

    #[ORM\Column(type: 'string', length: 5)]
    private string $countryCode;

    #[ORM\Column(type: 'string', length: 5)]
    private string $psc;

    #[ORM\Column(type: 'decimal', precision: 9, scale: 6, nullable: true)]
    private ?float $latitude;

    #[ORM\Column(type: 'decimal', precision: 9, scale: 6, nullable: true)]
    private ?float $longitude;


    public function __construct()
    {
  //      $this->cityUsers = new ArrayCollection();
    }

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
/*
    public function getCityUsers(): Collection
    {
        return $this->cityUsers;
    }
*/
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
        return $this->id;
    }

    public function getPsc(): string
    {
        return $this->psc;
    }

    public function setPsc(string $psc): Citys
    {
        $this->psc = $psc;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): Citys
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): Citys
    {
        $this->longitude = $longitude;
        return $this;
    }




}