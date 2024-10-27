<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use DateTimeImmutable;

#[ORM\Entity]
#[ORM\Table(name: 'adoptions')]

class Adoption
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "Users", inversedBy: "adoptionsAsOwner")]
    #[ORM\JoinColumn(name: "owner_id", referencedColumnName: "id")]
    private Users $owner;

    #[ORM\ManyToOne(targetEntity: "Users", inversedBy: "adoptionsAsAzyl")]
    #[ORM\JoinColumn(name: "azyl_id", referencedColumnName: "id")]
    private Users $azyl;

    #[ManyToOne(targetEntity: "Animal", inversedBy: "adoptions")]
    #[ORM\JoinColumn(name: "animal_id", referencedColumnName: "id")]
    private Animal $animal;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'boolean')]
    private bool $deleted;

    #[ORM\Column(type: 'boolean')]
    private bool $confirmed;

    #[ORM\Column(type: 'boolean')]
    private bool $canceled;

    #[ORM\OneToMany(targetEntity: "AdoptionAction", mappedBy: "adoption")]
    #[ORM\JoinColumn(name: "adoption_id", referencedColumnName: "id")]
    private AdoptionAction $adoptionActions;

    public function getOwner(): Users
    {
        return $this->owner;
    }

    public function setOwner(Users $owner): Adoption
    {
        $this->owner = $owner;
        return $this;
    }

    public function getAzyl(): Users
    {
        return $this->azyl;
    }

    public function setAzyl(Users $azyl): Adoption
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getAnimal(): Animal
    {
        return $this->animal;
    }

    public function setAnimal(Animal $animal): Adoption
    {
        $this->animal = $animal;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): Adoption
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): Adoption
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): Adoption
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): Adoption
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    public function isCanceled(): bool
    {
        return $this->canceled;
    }

    public function setCanceled(bool $canceled): Adoption
    {
        $this->canceled = $canceled;
        return $this;
    }

    public function getAdoptionActions(): AdoptionAction
    {
        return $this->adoptionActions;
    }

    public function setAdoptionActions(AdoptionAction $adoptionActions): Adoption
    {
        $this->adoptionActions = $adoptionActions;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }





}