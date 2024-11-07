<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'owners')]

class Owner
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $ownerName;

    #[ORM\Column(type: 'string', length: 1024)]
    private string $description;

    #[ORM\Column(type: 'string', length: 255)]
    private string $phoneNumber;

    #[ORM\OneToMany(mappedBy: "owner", targetEntity: "Photo")]
    private Collection $photos;

    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: Users::class)]
    private Users $user;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'adoptionsAsOwner')]
    private Users $owner;


    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Owner
    {
        $this->id = $id;
        return $this;
    }

    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    public function setOwnerName(string $ownerName): Owner
    {
        $this->ownerName = $ownerName;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Owner
    {
        $this->description = $description;
        return $this;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): Owner
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): Owner
    {
        $this->user = $user;
        return $this;
    }


}