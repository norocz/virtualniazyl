<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'animals')]
class Animal
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "Azyl", inversedBy: "animals")]
    #[ORM\JoinColumn(name: "azyl_id", referencedColumnName: "id")]
    private Azyl $azyl;

    #[ORM\ManyToOne(targetEntity: "Species", inversedBy: "animals")]
    #[ORM\JoinColumn(name: "species_id", referencedColumnName: "id")]
    private Species $species;

    #[ORM\Column(type: 'float', length: 5, scale: 2, nullable: true)]
    private ?int $age;

    #[ORM\Column(type: 'string', length: 255)]
    private string $breed;

    #[ORM\OneToMany(targetEntity: "Photo", mappedBy: "animal")]
    #[ORM\JoinColumn(name: "animal_id", referencedColumnName: "id")]
    private Photo $photos;

    #[ORM\OneToMany(targetEntity: "Adoption", mappedBy: "animal")]
    #[ORM\JoinColumn(name: "animal_id", referencedColumnName: "id")]
    private Adoption $adoption;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 1024)]
    private string $description;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $birthDate;

    #[ORM\Column(type: 'boolean')]
    private bool $toAdoption;

    #[ORM\Column(type: 'boolean')]
    private bool $adopted;
    #[ORM\Column(type: 'boolean')]
    private bool $isDeleted;

    public function getAzyl(): Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(Azyl $azyl): Animal
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getSpecies(): Species
    {
        return $this->species;
    }

    public function setSpecies(Species $species): Animal
    {
        $this->species = $species;
        return $this;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): Animal
    {
        $this->age = $age;
        return $this;
    }

    public function getBreed(): string
    {
        return $this->breed;
    }

    public function setBreed(string $breed): Animal
    {
        $this->breed = $breed;
        return $this;
    }

    public function getPhotos(): Photo
    {
        return $this->photos;
    }

    public function setPhotos(Photo $photos): Animal
    {
        $this->photos = $photos;
        return $this;
    }

    public function getAdoption(): Adoption
    {
        return $this->adoption;
    }

    public function setAdoption(Adoption $adoption): Animal
    {
        $this->adoption = $adoption;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Animal
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Animal
    {
        $this->description = $description;
        return $this;
    }

    public function getBirthDate(): DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(DateTimeImmutable $birthDate): Animal
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function isAdopted(): bool
    {
        return $this->adopted;
    }

    public function setAdopted(bool $adopted): Animal
    {
        $this->adopted = $adopted;
        return $this;
    }

    public function isToAdoption(): bool
    {
        return $this->toAdoption;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setToAdoption(bool $toAdoption): Animal
    {
        $this->toAdoption = $toAdoption;
        return $this;
    }

    public function setIsDeleted(bool $isDeleted): Animal
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'azyl' => $this->azyl->toArray(),
            'species' => $this->species->toArray(),
            'age' => $this->age,
            'breed' => $this->breed,
            'photos' => $this->photos->toArray(),
            'adoption' => $this->adoption->toArray(),
            'name' => $this->name,
            'description' => $this->description,
            'birthDate' => $this->birthDate->format('Y-m-d H:i:s'),
            'adopted' => $this->adopted,
            'isDeleted' => $this->isDeleted,
        ];
    }


}
