<?php
declare(strict_types=1);

namespace App\Model\Orm\Repository;

use App\Model\Orm\Entity\Messages;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class MessagesRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $em, string $entityClass = Messages::class)
    {
        parent::__construct($em, $em->getClassMetadata($entityClass));
    }

    public function countUnreadMessages(int $receiverId): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m)')
            ->andWhere('m.readed = :readed')
            ->andWhere('m.receiver = :receiverId')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('readed', false)
            ->setParameter('receiverId', $receiverId)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getMessagesById(int $id): ?Messages
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :id')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('id', $id)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getMessagesByReceiverId(int $receiverId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.receiver = :receiverId')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('receiverId', $receiverId)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function getMessagesBySenderId(int $senderId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.sender = :senderId')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('senderId', $senderId)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function getMessagesByReceiverIdAndSenderId(int $receiverId, int $senderId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.receiver = :receiverId')
            ->andWhere('m.sender = :senderId')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('receiverId', $receiverId)
            ->setParameter('senderId', $senderId)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function getMessagesByReceiverIdAndSenderIdAndType(int $receiverId, int $senderId, string $type): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.receiver = :receiverId')
            ->andWhere('m.sender = :senderId')
            ->andWhere('m.type = :type')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('receiverId', $receiverId)
            ->setParameter('senderId', $senderId)
            ->setParameter('type', $type)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function getMessagesByReceiverIdAndType(int $receiverId, string $type): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.receiver = :receiverId')
            ->andWhere('m.type = :type')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('receiverId', $receiverId)
            ->setParameter('type', $type)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function getMessagesBySenderIdAndType(int $senderId, string $type): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.sender = :senderId')
            ->andWhere('m.type = :type')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('senderId', $senderId)
            ->setParameter('type', $type)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function getMessagesByType(string $type): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.type = :type')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('type', $type)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function getMessagesByReceiverIdAndReaded(int $receiverId, bool $readed): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.receiver = :receiverId')
            ->andWhere('m.readed = :readed')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('receiverId', $receiverId)
            ->setParameter('readed', $readed)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function getMessagesBySenderIdAndReaded(int $senderId, bool $readed): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.sender = :senderId')
            ->andWhere('m.readed = :readed')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('senderId', $senderId)
            ->setParameter('readed', $readed)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }

    public function getMessagesByReceiverIdAndSenderIdAndReaded(int $receiverId, int $senderId, bool $readed): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.receiver = :receiverId')
            ->andWhere('m.sender = :senderId')
            ->andWhere('m.readed = :readed')
            ->andWhere('m.deletedAt IS NULL OR m.deletedAt > :now')
            ->setParameter('receiverId', $receiverId)
            ->setParameter('senderId', $senderId)
            ->setParameter('readed', $readed)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult();
    }


    public function setMessageReadedStatus(int $id, bool $readed = true): void
    {
        $msg = $this->findOneBy(['id' => $id, 'readed' => $readed]);
        $msg->setReaded($readed);
        $this->getEntityManager()->persist($msg);
        $this->getEntityManager()->flush();

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
}