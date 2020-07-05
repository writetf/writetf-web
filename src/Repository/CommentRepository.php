<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\Community;
use App\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findLatest(int $page = 1, Post $post = null): Paginator
    {
        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.post', 'p')
            ->where('c.parent is null')
            ->orderBy('c.publishedAt', 'ASC');
        if (null !== $post) {
            $qb->andWhere('c.post = :post')
                ->setParameter('post', $post);
        }
        return (new Paginator($qb))->paginate($page);
    }
}
