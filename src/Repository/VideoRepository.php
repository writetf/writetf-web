<?php

namespace App\Repository;

use App\Entity\Video;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Video::class);
    }

    public function findByProductName($product, $offset = 0, $limit = 12)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('v')
            ->from(Video::class, 'v')
            ->leftJoin('v.videoCategory', 'vc')
            ->where('vc.product = :product')
            ->setParameter('product', $product)
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
