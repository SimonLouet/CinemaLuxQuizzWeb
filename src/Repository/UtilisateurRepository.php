<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    // /**
    //  * @return Utilisateur[] Returns an array of Utilisateur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public function FirstReponse($idQuestion)
    {
      $rawSql = "SELECT e.id, e.login,rp.correct FROM utilisateur e , reponse r, reponse_reponse_possible rrp, reponse_possible rp Where rp.id = rrp.reponse_possible_id AND r.id = rrp.reponse_id AND r.utilisateur_id = e.id AND r.question_id = ".$idQuestion." ORDER BY r.timereponse ";
      $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
      $stmt->execute([]);
      return $stmt->fetchAll();
    }

    public function Score($idPartie)
    {
      $rawSql = "SELECT u.login, COUNT(r.id) as score from utilisateur_partie up,utilisateur u , reponse r ,reponse_reponse_possible rrp ,reponse_possible rp ,question q WHERE rrp.reponse_id = r.id AND rrp.reponse_possible_id = rp.id AND rp.question_id = q.id AND up.utilisateur_id = u.id AND r.utilisateur_id = u.id AND rp.correct = 1 AND partie_id = ".$idPartie."AND q.partie_id = ".$idPartie." GROUP BY (r.utilisateur_id) ORDER BY score DESC ;";
      $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
      $stmt->execute([]);
      return $stmt->fetchAll();
    }
    /*
    public function findOneBySomeField($value): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
