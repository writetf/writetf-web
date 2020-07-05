<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Community;
use App\Pagination\Paginator;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class PostRepository
 * @package App\Repository
 * @method Post[]    findAll()
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findLatest(int $page = 1, User $user = null, Community $community = null, User $author = null): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('a')
            ->innerJoin('p.author', 'a')
            ->leftJoin('p.community', 'c')
            ->leftJoin('c.category', 'cc')
            ->where('p.publishedAt <= :now')
            ->andWhere('cc.status = :status')
            ->orderBy('p.latestActivityAt', 'DESC')
            ->setParameter('now', new \DateTime())
            ->setParameter('status', 'enable');
        if (null !== $community) {
            $qb->andWhere('p.community = :community')
                ->setParameter('community', $community);
        }
        if (null !== $user) {
            $qb->andWhere('p.author = :user')
                ->setParameter('user', $user);
        }
        if ($community === null && $user === null) {
            if ($author === null) {
                $qb->andWhere('p.community is not null');
            } else {
                $qb->andWhere('p.community is not null or p.author = :author')
                    ->setParameter('author', $author);
            }

        }
        return (new Paginator($qb))->paginate($page);
    }

    /**
     * @return Post[]
     */
    public function findBySearchQuery(string $query, int $limit = Post::NUM_ITEMS): array
    {
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === \count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('p');

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('p.title LIKE :t_' . $key)
                ->setParameter('t_' . $key, '%' . $term . '%');
        }

        return $queryBuilder
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Transforms the search string into an array of search terms.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $searchQuery = trim(preg_replace('/[[:space:]]+/', ' ', $searchQuery));
        $terms = array_unique(explode(' ', $searchQuery));

        // ignore the search terms that are too short
        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }
}
