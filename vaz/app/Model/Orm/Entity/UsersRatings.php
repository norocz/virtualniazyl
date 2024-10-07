<?php
declare(strict_types=1);

namespace App\Model\Orm\Entity;


use App\Model\Orm\Enums\RoleTypeEnum;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
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
    private string $reviewer;

    #[ORM\Column(type: 'float', nullable: true)]
    private float $rating;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'userRatings', targetEntity: 'Photo')]
    private Collection $photos;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'userRatings')]
    private Collection $user;

    /**
     * @param int $id
     * @param string $review
     * @param string $reviewer
     * @param float $rating
     * @param DateTimeImmutable $createdAt
     * @param Collection $photos
     * @param Collection $user
     */
    public function __construct(int $id, string $review, string $reviewer, float $rating, DateTimeImmutable $createdAt, Collection $photos, Collection $user)
    {
        $this->id = $id;
        $this->review = $review;
        $this->reviewer = $reviewer;
        $this->rating = $rating;
        $this->createdAt = $createdAt;
        $this->photos = $photos;
        $this->user = $user;
    }

    public function getReview(): string
    {
        return $this->review;
    }

    public function setReview(string $review): UserRatings
    {
        $this->review = $review;
        return $this;
    }

    public function getReviewer(): string
    {
        return $this->reviewer;
    }

    public function setReviewer(string $reviewer): UserRatings
    {
        $this->reviewer = $reviewer;
        return $this;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function setRating(float $rating): UserRatings
    {
        $this->rating = $rating;
        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): UserRatings
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function setPhotos(Collection $photos): UserRatings
    {
        $this->photos = $photos;
        return $this;
    }

    public function getUser(): Collection
    {
        return $this->user;
    }

    public function setUser(Collection $user): UserRatings
    {
        $this->user = $user;
        return $this;
    }




}