<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Symfony\Component\Routing\Annotation\Route;

class MailController
{
    /**
     * @param PostRepository $posts
     * @param CommentRepository $comments
     * @return
     */
    public function mail(PostRepository $posts, CommentRepository $comments)
    {
        /** @var Post $post */
        $post = $posts->find('rgpcb8q9sxo');
        /** @var Comment $comment */
        $comment = $comments->find('q2wmse4a8j6');
        $comment->setContent('https://tse1.mm.bing.net/th?id=OGC.e96b2ae4c3e9e55bf0a571ca43751cd5&pid=Api&rurl=https%3a%2f%2fmedia.tenor.co%2fimages%2fe96b2ae4c3e9e55bf0a571ca43751cd5%2fraw&ehk=vqKCmTIWDJLP1BmngUrBhg');
        return $this->render('email/comment.html.twig', [
            'post' => $post,
            'comment' => $comment,
            'to' => 'ga9xvn@gmail.com',
            'isFollower' => false
        ]);
    }
}
