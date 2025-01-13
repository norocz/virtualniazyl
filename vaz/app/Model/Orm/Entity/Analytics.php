<?php

declare(strict_types = 1);

namespace App\Model\Orm\Entity;

use App\Model\Orm\Entity\Animal;
use App\Model\Orm\Entity\Azyl;
use App\Model\Orm\Entity\Users;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;


#[ORM\Entity]
#[ORM\Table(name: 'analytics')]

class Analytics
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;


    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date;

    #[ORM\ManyToOne(targetEntity: Azyl::class, cascade: ['persist'], inversedBy: "azyl")]
    private ?Azyl $azyl;

    #[ORM\ManyToOne(targetEntity: Animal::class, cascade: ['persist'], inversedBy: "animal")]
    private ?Animal $animal;

    #[ORM\ManyToOne(targetEntity: Users::class, cascade: ['persist'],  inversedBy: 'users')]
    private ?Users $user;

    #[ORM\Column(type: 'string', length: 512)]
    private string $comment;

    #[ORM\Column(type: 'string', length: 255)]
    private string $ipAdress; //nonregistredip

    #[ORM\Column(type: 'string', length: 255)]
    private string $action; //login,logout,visit,registration,showadoption,

    #[ORM\Column(type: 'integer', length: 255)]
    private int $tempId; //temporary non registred user id

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): Analitics
    {
        $this->date = $date;
        return $this;
    }

    public function getAzyl(): Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(Azyl $azyl): Analytics
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getAnimal(): Animal
    {
        return $this->animal;
    }

    public function setAnimal(Animal $animal): Analytics
    {
        $this->animal = $animal;
        return $this;
    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): Analytics
    {
        $this->user = $user;
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): Analytics
    {
        $this->comment = $comment;
        return $this;
    }

    public function getIpAdress(): string
    {
        return $this->ipAdress;
    }

    public function setIpAdress(string $ipAdress): Analytics
    {
        $this->ipAdress = $ipAdress;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): Analytics
    {
        $this->action = $action;
        return $this;
    }

    public function getTempId(): int
    {
        return $this->tempId;
    }

    public function setTempId(int $tempId): Analytics
    {
        $this->tempId = $tempId;
        return $this;
    }


}