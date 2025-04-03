<?php

namespace App\Model\Orm\Entity;

use App\Model\Orm\Enums\SexTypeEnum;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'species')]
class Species
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $description;

    #[ORM\Column(type:SexTypeEnum::SEX_TYPE_ENUM, length: 255)]
    private string $sex;

    #[ORM\OneToMany(targetEntity: "Animal", mappedBy: "species")]
    private Collection $animals;

    #[ORM\Column(type: 'string', length: 1024)]
    private ?string $tags;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Species
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Species
    {
        $this->description = $description;
        return $this;
    }

    public function getSex(): string
    {
        return $this->sex;
    }

    public function setSex(string $sex): Species
    {
        $this->sex = $sex;
        return $this;
    }

    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): Species
    {
        $this->tags = $tags;
        return $this;
    }



}