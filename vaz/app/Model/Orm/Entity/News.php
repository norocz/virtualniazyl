<?php

declare(strict_types=1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Doctrine\ORM\Mapping as ORM;
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

    #[ORM\Column(type: 'text', length: 10024)]
    private string $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $visibleFrom;

    #[ORM\ManyToOne(targetEntity: "Users", cascade: ['persist'], inversedBy: 'news')]
    private ?Users $author;

    #[ORM\Column(type: 'boolean')]
    private $deleted;

    #[ORM\Column(type: 'boolean')]
    private $global;
    #[ORM\Column(type: 'boolean')]
    private $important;

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

    /**
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     * @return News
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGlobal()
    {
        return $this->global;
    }

    /**
     * @param mixed $global
     * @return News
     */
    public function setGlobal($global)
    {
        $this->global = $global;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImportant()
    {
        return $this->important;
    }

    /**
     * @param mixed $important
     * @return News
     */
    public function setImportant($important)
    {
        $this->important = $important;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
