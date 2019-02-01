<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    // /**
    //  * @return Question[] Returns an array of Question objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    public function findByPartieOrderByNumero($partie): array
    {
		 $qb = $this->createQueryBuilder('e')
            ->andWhere('e.partie = '. $partie->getId())
            ->orderBy('e.numero', 'ASC')
            ->getQuery();

        return $qb->execute();

    }
    public function ReponseStatistique($idQuestion)
    {
      $rawSql = "SELECT e.login,e.mail,rp.correct,rp.libelle,r.timereponse FROM utilisateur e , reponse r, reponse_reponse_possible rrp, reponse_possible rp Where rp.id = rrp.reponse_possible_id AND r.id = rrp.reponse_id AND r.utilisateur_id = e.id AND r.question_id = ".$idQuestion;
      $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
      $stmt->execute([]);
      return $stmt->fetchAll();
    }

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
