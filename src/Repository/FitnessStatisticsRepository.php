<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FitnessStatistics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FitnessStatistics|null find($id, $lockMode = null, $lockVersion = null)
 * @method FitnessStatistics|null findOneBy(array $criteria, array $orderBy = null)
 * @method FitnessStatistics[]    findAll()
 * @method FitnessStatistics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FitnessStatisticsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FitnessStatistics::class);
    }

    // /**
    //  * @return FitnessStatistics[] Returns an array of FitnessStatistics objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FitnessStatistics
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
