<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Users;
use App\Model\Orm\Enums\RoleTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;


class UsersRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $entityClass = Users::class)
    {
        parent::__construct($em, $em->getClassMetadata($entityClass));
    }

    /**
     * @return Users[]
     */
    public function fetchAll(): array
    {
        return $this->createQueryBuilder('u')
            ->getQuery()
            ->getResult();
    }

    public function addUser(Users $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }


    public function findOneBy(array $criteria, ?array $orderBy = null) : ?Users
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * @param Users $user
     */
    public function remove(Users $user): void
    {
        $this->getEntityManager()->remove($user);
    }

    /**
     * Flushes all changes to objects that have been queued up to now to the database.
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Soft deletes the user by setting the 'deleted' property to true.
     * @param Users $user
     */
    public function delete(Users $user): void
    {
        $user->setDeleted(true);
        $this->getEntityManager()->flush();
    }

    public function getUserByEmail($email) : ?Users
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function getUserByUserName($userName) : ?Users
    {
        return $this->findOneBy(['userName' => $userName]);
    }

    public function getUserById(int $user) : ?Users
    {
        return $this->findOneBy(['id' => $user]);
    }

    public function getUserByAzylId(int $azyl) : ?Users
    {
        return $this->findOneBy(['azyl' => $azyl]);
    }

    public function setPassword(string $hash)
    {

    }

    public function getUserByMailVerifyToken(mixed $vrf)
    {
        return $this->findOneBy(['mailVerifyToken' => $vrf]);
    }

    public function CountNewUsers() //count users type user
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.role = :type')
            ->setParameter('type', RoleTypeEnum::ROLE_USER)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function CountAzyls() //count users type user
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.role = :type')
            ->setParameter('type', RoleTypeEnum::ROLE_AZYL)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function CountUsers() //count users type user
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}