<?php

namespace App\Repository;

use App\Entity\FavoriteNews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FavoriteNews|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavoriteNews|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavoriteNews[]    findAll()
 * @method FavoriteNews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavoriteNewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavoriteNews::class);
    }

    // /**
    //  * @return FavoriteNews[] Returns an array of FavoriteNews objects
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
    public function findOneBySomeField($value): ?FavoriteNews
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
