<?php

namespace App\Repository;

use App\Entity\ReponsePossible;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ReponsePossible|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReponsePossible|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReponsePossible[]    findAll()
 * @method ReponsePossible[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReponsePossibleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ReponsePossible::class);
    }

    // /**
    //  * @return ReponsePossible[] Returns an array of ReponsePossible objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReponsePossible
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
