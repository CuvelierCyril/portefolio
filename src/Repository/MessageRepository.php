<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findAllToday($datetime)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.date LIKE :val')
            ->setParameter('val', $datetime)
            ->orderBy('m.date', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByDate($offset){
        return $this->createQueryBuilder('m')
            ->orderBy('m.date', 'DESC')
            ->setMaxResults(25)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findLastByIpadress($ip){
        return $this->createQueryBuilder('m')
            ->andWhere('m.ipadress = :val')
            ->setParameter('val', $ip)
            ->orderBy('m.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
