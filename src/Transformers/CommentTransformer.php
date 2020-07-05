<?php

namespace App\Transformers;

use App\Entity\Comment;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CommentTransformer extends BaseTransformer implements TransformerInterface
{
    protected $userTransformer;

    public function __construct(RequestStack $requestStack, CacheInterface $cache, UserTransformer $userTransformer)
    {
        parent::__construct($requestStack, $cache);
        $this->userTransformer = $userTransformer;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'comment';
    }

    /**
     * @param Comment $comment
     * @return array
     */
    public function getAttributes($comment)
    {
        return [
            'content' => $comment->getContent(),
            'published_at' => $this->dateFormat($comment->getPublishedAt()),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getIncludes($entity)
    {
        return [];
    }

    /**
     * @param Comment $comment
     * @return array
     * @throws InvalidArgumentException
     */
    public function getRelationships($comment)
    {
        return [
            'author' => $this->userTransformer->transform($comment->getAuthor()),
        ];
    }
}
