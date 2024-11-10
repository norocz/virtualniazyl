<?php

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Loginout;
use App\Model\Orm\Entity\Users;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;


class LoginoutRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $class = Loginout::class)
    {
        parent::__construct($em, $em->getClassMetadata($class));
    }

    public function findAll()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();

    }

    public function findById(int $id)
    {
        return $this->createQueryBuilder('l')
            ->where('l.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function findByUser(Users $user)
    {
        return $this->createQueryBuilder('l')
            ->where('l.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

    }

    public function saveLog(Loginout $loginout): void
    {
        $this->getEntityManager()->persist($loginout);
        $this->getEntityManager()->flush();

    }

}