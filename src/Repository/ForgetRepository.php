<?php

namespace App\Repository;

use App\Entity\Forget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Forget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forget|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forget[]    findAll()
 * @method Forget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forget::class);
    }

    // /**
    //  * @return Forget[] Returns an array of Forget objects
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
    public function findOneBySomeField($value): ?Forget
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
