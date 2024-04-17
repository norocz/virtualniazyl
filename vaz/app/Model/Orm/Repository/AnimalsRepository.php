<?php

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Animal;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;


class AnimalsRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $class = Animal::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }

    public function findAll(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function findBySpecies(string $species): array
    {
        return $this->findBy(['species' => $species]);
    }

    public function fetchAll(): array
    {
        return $this->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
    }

    public function saveAnimal(Animal $animal): void
    {
        $this->getEntityManager()->persist($animal);
        $this->getEntityManager()->flush();
    }

    public function findByName(string $name): ?Animal
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findById(int $id): ?Animal
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function deleteAnimal(Animal $animal): void
    {
        $this->getEntityManager()->remove($animal);
        $this->getEntityManager()->flush();
    }

    public function toArray($id): array
    {
        return $this->findOneBy(['id' => $id])->toArray();

    }

    public function persist(Animal $animal)
    {
        $this->getEntityManager()->persist($animal);
    }

    public function remove(Animal $animal)
    {
        $this->getEntityManager()->remove($animal);
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}