<?php

declare(strict_types=1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity]
#[ORM\Table(name: 'news')]

class News

{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text', length: 256000)]
    private string $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $visibleFrom;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'news')]
    private ?Users $author;

    #[ORM\ManyToOne(targetEntity: Azyl::class, inversedBy: 'news')]
    private ?Azyl $azyl;

    #[ORM\Column(type: 'boolean')]
    private bool $deleted;

    #[ORM\Column(type: 'boolean')]
    private bool $global;
    #[ORM\Column(type: 'boolean')]
    private bool $important;

    #[ORM\Column(type: 'boolean')]
    private bool $pined;

    public function toArray(): array //return array of all fields
    {

        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'global' => $this->global,
            'important' => $this->important,
            'author' => $this->author,
            'azyl' => $this->azyl,
            'deleted' => $this->deleted
        ];

    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): News
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): News
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): News
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): News
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getVisibleFrom(): DateTimeImmutable
    {
        return $this->visibleFrom;
    }

    public function setVisibleFrom(DateTimeImmutable $visibleFrom): News
    {
        $this->visibleFrom = $visibleFrom;
        return $this;
    }

    public function getAuthor(): ?Users
    {
        return $this->author;
    }

    public function setAuthor(Users $author): News
    {
        $this->author = $author;
        return $this;
    }

    public function getDeleted():bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted):static
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function getGlobal():bool
    {
        return $this->global;
    }

    public function setGlobal(bool $global): static
    {
        $this->global = $global;
        return $this;
    }

    public function getImportant():bool
    {
        return $this->important;
    }

    public function setImportant(bool $important): static
    {
        $this->important = $important;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAzyl(): ?Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(?Azyl $azyl): News
    {
        $this->azyl = $azyl;
        return $this;
    }

    public function getPined(): bool
    {
        return $this->pined;
    }

    public function setPined(bool $pined): News
    {
        $this->pined = $pined;
        return $this;
    }

}
