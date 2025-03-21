<?php

declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\ContractParts;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ContractPartsRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $entityClass = ContractParts::class)
    {
        parent::__construct($em, $em->getClassMetadata($entityClass));
    }

    public function save(ContractParts $contractParts): void
    {
        $this->getEntityManager()->persist($contractParts);
        $this->getEntityManager()->flush();
    }

    public function persist(ContractParts $contractParts): void
    {
        $this->getEntityManager()->persist($contractParts);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(ContractParts $contractParts): void
    {
        $this->getEntityManager()->remove($contractParts);
        $this->getEntityManager()->flush();
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    public function findOneById(int $id): ?ContractParts
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findAll()
    {
        return $this->createQueryBuilder('s')
            ->getQuery()
            ->getResult();
    }

    public function fetchAll()
    {
        return $this->createQueryBuilder('s')
            ->getQuery()
            ->getResult();
    }

    public function fetchAllDatagrid()
    {
        return $this->createQueryBuilder('s')
            ->where('s.inUsage = true')
            ->getQuery()
            ->getResult();
    }
}