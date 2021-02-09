<?php

namespace App\Repository;

use App\Entity\DDNS;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method DDNS|null find($id, $lockMode = null, $lockVersion = null)
 * @method DDNS|null findOneBy(array $criteria, array $orderBy = null)
 * @method DDNS[]    findAll()
 */
class DDNSRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DDNS::class);
    }
}
