<?php

namespace App\Repository;

use App\Entity\Bateaux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Bateaux|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bateaux|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bateaux[]    findAll()
 * @method Bateaux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BateauxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bateaux::class);
    }

    public function orderByClient($orderby,$mode){
        return $this->createQueryBuilder('r')
            ->leftJoin('r.owner','owner')
            ->orderBy("$orderby", $mode)
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Bateaux[] Returns an array of Bateaux objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bateaux
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
