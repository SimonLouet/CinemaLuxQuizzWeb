<?php

namespace App\Repository;

use App\Entity\Partie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
* @method Partie|null find($id, $lockMode = null, $lockVersion = null)
* @method Partie|null findOneBy(array $criteria, array $orderBy = null)
* @method Partie[]    findAll()
* @method Partie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
*/
class PartieRepository extends ServiceEntityRepository
{
  public function __construct(RegistryInterface $registry)
  {
    parent::__construct($registry, Partie::class);
  }

  // /**
  //  * @return Partie[] Returns an array of Partie objects
  //  */
  /*
  public function findByExampleField($value)
  {
  return $this->createQueryBuilder('p')
  ->andWhere('p.exampleField = :val')
  ->setParameter('val', $value)
  ->orderBy('p.id', 'ASC')
  ->setMaxResults(10)
  ->getQuery()
  ->getResult()
  ;
}
*/
public function getNbQuestion() {

  return $this->createQueryBuilder('e')
  ->select('COUNT(e.// QUESTION: )')
  ->getQuery()
  ->getSingleScalarResult();

}

public function resetPartie($idPartie) {
  $rawSql = "DELETE r.* FROM reponse as r INNER JOIN question AS q ON r.question_id = q.id WHERE q.partie_id = ".$idPartie;
  $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
  $stmt->execute();
  return ;

}


/*
public function findOneBySomeField($value): ?Partie
{
return $this->createQueryBuilder('p')
->andWhere('p.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
*/
}
