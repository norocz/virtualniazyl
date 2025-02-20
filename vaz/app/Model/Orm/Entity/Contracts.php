<?php

declare(strict_types=1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "contracts")]
class Contracts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: "contracts")]
    #[ORM\JoinColumn(nullable: false)]
    private Users $user;

    #[ORM\ManyToOne(targetEntity: Azyl::class, inversedBy: "contracts")]
    #[ORM\JoinColumn(nullable: false)]
    private Azyl $azyl;

    #[ORM\ManyToMany(targetEntity: Animal::class, inversedBy: "contracts")]
    #[ORM\JoinTable(name: "contracts_animals")]
    private Collection $animals;

    #[ORM\ManyToMany(targetEntity: ContractParts::class, inversedBy: "contracts")]
    #[ORM\JoinTable(name: "contracts_contract_parts")]
    private Collection $contractParts;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
        $this->contractParts = new ArrayCollection();
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getUser(): Users { return $this->user; }
    public function getAzyl(): Azyl { return $this->azyl; }
    public function getAnimals(): Collection { return $this->animals; }
    public function getContractParts(): Collection { return $this->contractParts; }

    public function addAnimal(Animal $animal): void
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
        }
    }

    public function removeAnimal(Animal $animal): void
    {
        $this->animals->removeElement($animal);
    }

    public function setId(int $id): Contracts
    {
        $this->id = $id;
        return $this;
    }

    public function setName(string $name): Contracts
    {
        $this->name = $name;
        return $this;
    }

    public function setUser(Users $user): Contracts
    {
        $this->user = $user;
        return $this;
    }

    public function setAzyl(Azyl $azyl): Contracts
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function setAnimals(Collection $animals): Contracts
    {
        $this->animals = $animals;
        return $this;
    }

    public function setContractParts(Collection $contractParts): Contracts
    {
        $this->contractParts = $contractParts;
        return $this;
    }


}
