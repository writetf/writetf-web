<?php

namespace App\Repository;

use App\Entity\VideoCategory;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class VideoCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VideoCategory::class);
    }

    public function findByProductName($product)
    {
        return $this->findBy(
            [
                'product' => $product
            ],
            [
                'createdAt' => 'DESC'
            ]
        );
    }
}
