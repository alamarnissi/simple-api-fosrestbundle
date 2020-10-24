<?php

namespace App\Repository;

use App\Entity\ColisProducts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ColisProducts|null find($id, $lockMode = null, $lockVersion = null)
 * @method ColisProducts|null findOneBy(array $criteria, array $orderBy = null)
 * @method ColisProducts[]    findAll()
 * @method ColisProducts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColisProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ColisProducts::class);
    }

    // /**
    //  * @return ColisProducts[] Returns an array of ColisProducts objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ColisProducts
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
