<?php

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Community;

class CacheService
{
    public static function getPostKey(Post $post)
    {
        return 'post-' . hash('sha256', serialize($post));
    }

    public static function getUserKey(User $user)
    {
        return 'user-' . hash('sha256', serialize($user));
    }

    public static function getCommentKey(Comment $comment)
    {
        return 'comment-' . hash('sha256', serialize($comment));
    }

    public static function getCommunityKey(Community $community)
    {
        return 'community-' . hash('sha256', serialize($community));
    }
}
