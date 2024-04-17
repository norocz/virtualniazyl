<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: 'azyls')]

class Azyl
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $azylName;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private string $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $bankAccount;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $bankCode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $bankSpecificCode;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $phoneNumber;

    #[ORM\OneToMany(mappedBy: "azyl", targetEntity: "Animal", cascade: ["o"])]
    private $animals;


    public function getAzylName(): string
    {
        return $this->azylName;
    }

    /**
     * @return mixed
     */
    public function getAnimals() : Collection
    {
        return $this->animals;
    }

    public function setAzylName(string $azylName): Azyl
    {
        $this->azylName = $azylName;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Azyl
    {
        $this->description = $description;
        return $this;
    }

    public function getBankAccount(): string
    {
        return $this->bankAccount;
    }

    public function setBankAccount(string $bankAccount): Azyl
    {
        $this->bankAccount = $bankAccount;
        return $this;
    }

    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    public function setBankCode(string $bankCode): Azyl
    {
        $this->bankCode = $bankCode;
        return $this;
    }

    public function getBankSpecificCode(): string
    {
        return $this->bankSpecificCode;
    }

    public function setBankSpecificCode(string $bankSpecificCode): Azyl
    {
        $this->bankSpecificCode = $bankSpecificCode;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): Azyl
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

}


