<?php

namespace App\Transformers;

use App\Entity\Post;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PostTransformer extends BaseTransformer
{
    protected $userTransformer;
    protected $commentTransformer;
    protected $communityTransformer;

    public function __construct(
        RequestStack $requestStack,
        UserTransformer $userTransformer,
        CommentTransformer $commentTransformer,
        CommunityTransformer $communityTransformer,
        CacheInterface $cache
    ) {
        parent::__construct($requestStack, $cache);
        $this->userTransformer = $userTransformer;
        $this->commentTransformer = $commentTransformer;
        $this->communityTransformer = $communityTransformer;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'post';
    }

    /**
     * @param Post $post
     * @return array
     */
    public function getAttributes($post)
    {
        return [
            'title' => $post->getTitle(),
            'content' => $post->getContent(),
            'thumbnail' => $post->getThumbnail() ? $this->imageFormat($post->getThumbnail()) : null,
            'published_at' => $this->dateFormat($post->getPublishedAt()),
            'updated_at' => $this->dateFormat($post->getUpdatedAt()),
            'commented_at' => $this->dateFormat($post->getCommentedAt()),
            'latest_activity_at' => $this->dateFormat($post->getLatestActivityAt()),
        ];
    }

    /**
     * @param Post $post
     * @return array
     * @throws InvalidArgumentException
     */
    public function getRelationships($post)
    {
        return [
            'author' => $this->userTransformer->transform($post->getAuthor()),
            'comments' => $this->getCommentsRelationship($post),
            'community' => $this->communityTransformer->transform($post->getCommunity()),
        ];
    }

    /**
     * @param Post $post
     * @return array
     * @throws InvalidArgumentException
     */
    protected function getCommentsRelationship(Post $post)
    {
        $comments = $post->getComments();
        $commentsData = [];
        foreach ($comments as $comment) {
            $commentsData[] = $this->commentTransformer->transform($comment);
        }

        return $commentsData;
    }

    /**
     * @param Post $post
     * @return array
     */
    public function getIncludes($post)
    {
        return [
            'comments' => $post->getComments()->count(),
            'views' => $post->getViews()->count(),
            'love' => $post->getLove()->count(),
        ];
    }
}
