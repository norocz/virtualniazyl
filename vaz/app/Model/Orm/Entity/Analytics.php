<?php

declare(strict_types = 1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;


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
    private string $host; //host name

    #[ORM\Column(type: 'string', length: 255)]
    private string $name; //presenter name

    #[ORM\Column(type: 'string', length: 255)]
    private string $action; //login,logout,visit,registration,showadoption,

    #[ORM\Column(type: 'string', length: 255)]
    private string $tempId; //temporary non registred user id

    #[ORM\Column(type: 'string', length: 1024)]
    private string $params; //parametry odkazu

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): Analytics
    {
        $this->date = $date;
        return $this;
    }

    public function getAzyl(): ?Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(?Azyl $azyl): Analytics
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): Analytics
    {
        $this->animal = $animal;
        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): Analytics
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

    public function getTempId(): string
    {
        return $this->tempId;
    }

    public function setTempId(string $tempId): Analytics
    {
        $this->tempId = $tempId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Analytics
    {
        $this->name = $name;
        return $this;
    }

    public function setHost(string $host): Analytics
    {
        $this->host = $host;
        return $this;
    }
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $params
     */
    public function setParams(string $params): void
    {
        $this->params = $params;
    }

    public function getParams(): string
    {
        return $this->params;
    }

}