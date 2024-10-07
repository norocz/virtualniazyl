<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Owner;
use App\Model\Orm\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class OwnersRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $entityClass = Owner::class)
    {
        parent::__construct($em, $em->getClassMetadata($entityClass));
    }

    public function fetchAll(): array
    {
        return $this->createQueryBuilder('u')
            ->getQuery()
            ->getResult();
    }

    public function addOwner(Owner|Users $owner): void
    {
        $this->getEntityManager()->persist($owner);
        $this->getEntityManager()->flush();
    }

    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    public function findOneByUser(Users $user): ?Owner
    {
        return parent::findOneBy(['user' => $user]);

    }

    public function remove(Owner|Users $owner): void
    {
        $this->getEntityManager()->remove($owner);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function delete(Owner|Users $owner): void
    {
        $this->getEntityManager()->remove($owner);
    }
}