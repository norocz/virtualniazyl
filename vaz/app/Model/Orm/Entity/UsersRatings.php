<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users_ratings')]
#[ORM\MappedSuperclass]

class UsersRatings
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[ORM\Column(type: 'string', length: 2048)]
    private string $review;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'reviews')]
    private Users $reviewer;

    #[ORM\Column(type: 'float', nullable: true)]
    private float $rating;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'userRatings', targetEntity: 'Photo')]
    private ?Collection $photos;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'userRatings')]
    private ?Users $user;

    #[ORM\ManyToOne(targetEntity: Azyl::class, inversedBy: 'reviewerRatings')]
    private ?Azyl $azyl;

       public function getReview(): string
    {
        return $this->review;
    }

    public function setReview(string $review): UsersRatings
    {
        $this->review = $review;
        return $this;
    }

    public function getReviewer(): Users
    {
        return $this->reviewer;
    }

    public function setReviewer(Users $reviewer): void
    {
        $this->reviewer = $reviewer;

    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): void
    {
        $this->rating = $rating;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt):void
    {
        $this->createdAt = $createdAt;
    }

    public function getPhotos(): ?Collection
    {
        return $this->photos;
    }

    public function setPhotos(?Collection $photos): void
    {
        $this->photos = $photos;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): void
    {
        $this->user = $user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): UsersRatings
    {
        $this->id = $id;
        return $this;
    }

    public function getAzyl(): ?Azyl
    {
        return $this->azyl;
    }

    public function setAzyl(?Azyl $azyl): UsersRatings
    {
        $this->azyl = $azyl;
        return $this;
    }

}