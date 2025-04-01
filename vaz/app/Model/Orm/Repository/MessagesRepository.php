<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Conversations;
use App\Model\Orm\Entity\Messages;
use App\Model\Orm\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class MessagesRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $entityClass = Messages::class)
    {
        parent::__construct($em, $em->getClassMetadata($entityClass));
    }


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countUnreadMessages(Users $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m)')
            ->andWhere('m.readed = :readed')
            ->andWhere('m.user = :User')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('readed', false)
            ->setParameter('User', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getSingleScalarResult();
    }



    public function findBytConversationMessages(string $conversationId): ?array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.conversation = :conversationId')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('conversationId', $conversationId)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByMessagesById(int $id): ?Messages
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :id')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('id', $id)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findByAndMergeConversations(array $conversationIds, string $lastConversation): int
    {
        return $this->createQueryBuilder('m') // ✅ Přidán alias
        ->update(Messages::class, 'm') // ✅ Opraven název entity
        ->set('m.conversation', ':lastConversation')
            ->where('m.conversation IN (:conversationIds)')
            ->setParameter('lastConversation', $lastConversation)
            ->setParameter('conversationIds', $conversationIds)
            ->getQuery()
            ->execute();
    }


    public function findByMessagesByType(string $type): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.type = :type')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('type', $type)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getMessagesById(int $id, Users $user): ?Messages
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :id')
            ->andWhere('m.user = :user')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function save(Messages $messages): void
    {
        $this->getEntityManager()->persist($messages);
        $this->getEntityManager()->flush();
    }

    public function remove(Messages $messages): void
    {
        $this->getEntityManager()->remove($messages);
        $this->getEntityManager()->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();

    }




}