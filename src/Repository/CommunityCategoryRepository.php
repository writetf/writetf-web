<?php

namespace App\Repository;

use App\Entity\CommunityCategory;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CommunityCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommunityCategory::class);
    }

    public function findAll()
    {
        $qb = $this->createQueryBuilder('cc');
        $qb->orderBy('cc.priority', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
