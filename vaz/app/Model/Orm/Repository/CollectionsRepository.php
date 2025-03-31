<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Collections;
use App\Repository\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CollectionsRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $class = Collections::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }

    public function findOneByKey(int $collectionKey): ?Collections
    {
        return $this->findOneBy(['collectionKey' => $collectionKey]);

    }

    public function fetchAllActive(): array
    {
        return $this->findBy(['isActive' => true, 'approved' => true]);
    }

    public function findByAzyl($azyl): array
    {
        return $this->findBy(['azyl' => $azyl]);
    }

    public function findByAzylActive($azyl): array
    {
        return $this->findBy(['azyl' => $azyl, 'isActive' => true, 'approved' => true]);

    }

    public function findByAzylNoActive($azyl): array
    {
        return $this->findBy(['azyl' => $azyl, 'isActive' => false, 'approved' => true]);

    }

    public function findByAzylWaiting($azyl): array
    {
        return $this->findBy(['azyl' => $azyl, 'approved' => false]);

    }

    public function save(Collections $collections): void
    {
        $this->getEntityManager()->persist($collections);
        $this->getEntityManager()->flush();

    }

    public function persist(Collections $collection): void
    {
        $this->getEntityManager()->persist($collection);
    }

    public function remove(Collections $collection): void
    {
        $this->getEntityManager()->remove($collection);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

}