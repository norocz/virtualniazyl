<?php

declare(strict_types = 1);

namespace App\Model\Orm;


use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
class Analitics
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;


    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date;

    #[ORM\Column(type: 'integer')]
    private int $azyl;

    #[ORM\Column(type: 'integer')]
    private int $animal;

    #[ORM\Column(type: 'integer')]
    private int $user;

    #[ORM\Column(type: 'string', length: 512)]
    private string $comment;

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

    public function getAzyl(): int
    {
        return $this->azyl;
    }

    public function setAzyl(int $azyl): Analitics
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getAnimal(): int
    {
        return $this->animal;
    }

    public function setAnimal(int $animal): Analitics
    {
        $this->animal = $animal;
        return $this;
    }

    public function getUser(): int
    {
        return $this->user;
    }

    public function setUser(int $user): Analitics
    {
        $this->user = $user;
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): Analitics
    {
        $this->comment = $comment;
        return $this;
    }




}