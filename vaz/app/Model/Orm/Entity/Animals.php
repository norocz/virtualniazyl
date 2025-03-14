<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use App\Model\Orm\Enums\AdoptionsTypeEnum;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'animals')]
class Animal
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "Azyl", cascade: ['persist'], inversedBy: "animals")]
    private Azyl $azyl;

    #[ORM\ManyToOne(targetEntity: "Species", inversedBy: "animals")]
    #[ORM\JoinColumn(name: "species_id", referencedColumnName: "id")]
    private Species $species;

    #[ORM\Column(type: 'float', length: 5, scale: 2, nullable: true)]
    private ?float $age;

    #[ORM\Column(type: 'string', length: 255)]
    private string $breed;

    #[ORM\OneToMany(mappedBy: "animal", targetEntity: "Photo")]
    #[ORM\JoinColumn(name: "animal_id", referencedColumnName: "id")]
    private ?Collection $photos;

    #[ORM\Column(type: 'boolean')]
    private ?bool $multiAdoption;
    #[ORM\Column(type: 'integer', length: 255)]
    private int $howMuch;

    #[ORM\OneToMany(mappedBy: "animal", targetEntity: Adoption::class)]
    #[ORM\JoinColumn(name: "adoption_id", referencedColumnName: "id")]
    private ?Collection $adoption;

    #[ORM\OneToMany(mappedBy: 'animal', targetEntity: Adoption::class)]
    #[ORM\JoinColumn(name: "adoption_id", referencedColumnName: "id")]
    private ?Collection $adoptions;

    #[ORM\Column(type: AdoptionsTypeEnum::ADOPTION_TYPE_ENUM, length: 255)]
    private string $adoptionType;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 1024)]
    private string $description;


    #[ORM\Column(type: 'boolean')]
    private bool $toAdoption;

    #[ORM\Column(type: 'boolean')]
    private bool $adopted;
    #[ORM\Column(type: 'boolean')]
    private bool $isDeleted;

    #[ORM\ManyToMany(targetEntity: Contracts::class, mappedBy: "animals")]
    private ?Collection $contracts;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $height;  //výška
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $weight; //hmotnost
    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $signed;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $birthdate;
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $reception;



    public function __construct()
    {
        $this->contracts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

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

    public function getAge(): ?int
    {
        return !is_null($this->birthdate) ? $this->birthdate->diff(new DateTimeImmutable())->y : null;
    }



    public function getBreed(): string
    {
        return $this->breed;
    }

    public function setBreed(string $breed): void
    {
        $this->breed = $breed;

    }

    public function getPhotos(): ?Collection
    {
        return $this->photos;
    }

    public function setPhotos(?Photo $photos): void
    {
        $this->photos = $photos;
    }

    /*
    public function getAdoption(): ?Collection
    {
        return $this->adoption;
    }

    public function setAdoption(?Adoption $adoption): void
    {
        $this->adoption = $adoption;
    }
*/
    public function getName(): ?string
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

    public function getBirthdate(): ?DateTimeImmutable
    {
        return $this->birthdate;
    }

    public function setBirthdate(?DateTimeImmutable $birthdate): Animal
    {
        $this->birthdate = $birthdate;
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


    public function getAdoptions(): Collection
    {
        return $this->adoptions;
    }

    public function getAdoption(): Collection
    {
        return $this->adoptions;
    }

    public function getAdoptionType(): string
    {
        return $this->adoptionType;
    }

    public function setAdoptionType(string $adoptionType): Animal
    {
        $this->adoptionType = $adoptionType;
        return $this;
    }

    public function getHowMuch(): int
    {
        return $this->howMuch;
    }

    public function setHowMuch(int $howMuch): Animal
    {
        $this->howMuch = $howMuch;
        return $this;
    }

    public function getMultiAdoption(): ?bool
    {
        return $this->multiAdoption;
    }

    public function setMultiAdoption(?bool $multiAdoption): Animal
    {
        $this->multiAdoption = $multiAdoption;
        return $this;
    }

    public function getContracts(): ?Collection
    {
        return $this->contracts;
    }

    public function setContracts(?Collection $contracts): Animal
    {
        $this->contracts = $contracts;
        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): Animal
    {
        $this->height = $height;
        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): Animal
    {
        $this->weight = $weight;
        return $this;
    }

    public function getSigned(): ?string
    {
        return $this->signed;
    }

    public function setSigned(?string $signed): Animal
    {
        $this->signed = $signed;
        return $this;
    }

    public function getReception(): ?DateTimeImmutable
    {
        return $this->reception;
    }

    public function setReception(?DateTimeImmutable $reception): Animal
    {
        $this->reception = $reception;
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
            'adoptions' => $this->adoptions->toArray(),
            'name' => $this->name,
            'description' => $this->description,
            'birthdate' => $this->birthdate->format('Y-m-d H:i:s'),
            'reception' => $this->reception->format('Y-m-d H:i:s'),
            'adopted' => $this->adopted,
            'isDeleted' => $this->isDeleted,
        ];
    }
}