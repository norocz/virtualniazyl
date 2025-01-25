<?php
declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

abstract class BaseRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $entityClass)
    {
        parent::__construct($em, $em->getClassMetadata($entityClass));
    }

    public function fetchAll(): array
    {
        return $this->findAll();
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere($criteria)
            ->orderBy($orderBy)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function remove($entity): void
    {
        $this->_em->remove($entity);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function delete($entity): void
    {
        if (method_exists($entity, 'setDeleted')) {
            $entity->setDeleted(true);
            $this->_em->persist($entity);
            $this->flush();
        } else {
            throw new \InvalidArgumentException('Entity does not have a setDeleted method.');
        }
    }
}
