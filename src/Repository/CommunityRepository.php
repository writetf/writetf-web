<?php

namespace App\Repository;

use App\Entity\Community;
use App\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Community|null find($id, $lockMode = null, $lockVersion = null)
 * @method Community|null findOneBy(array $criteria, array $orderBy = null)
 * @method Community[]    findAll()
 */
class CommunityRepository extends ServiceEntityRepository
{
    const MAX_TOP_ITEMS = 10;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Community::class);
    }

    public function findLatest()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->where('c.status = :status')
            ->setParameter('status', Community::STATUS_ENABLE);


        return $qb->getQuery()->getResult();
    }

    public function getTop()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->leftJoin('c.posts', 'p')
            ->where('c.id = p.community')
            ->groupBy('c.id')
            ->orderBy('count(p.id)', 'DESC')
            ->setMaxResults(self::MAX_TOP_ITEMS);

        return $qb->getQuery()->getResult();
    }
}
