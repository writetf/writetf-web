<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Controller\BaseController;
use App\Repository\PostRepository;
use App\Transformers\PostTransformer;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/v1/posts")
 */
class PostApiController extends BaseController
{
    /**
     * @Route("/", name="api_posts_index")
     * @param Request $request
     * @param PostRepository $posts
     * @param PostTransformer $transformer
     * @return Response
     * @throws InvalidArgumentException
     */
    public function posts(
        Request $request,
        PostRepository $posts,
        PostTransformer $transformer
    ): Response {
        $page = $request->query->get('page');
        if (empty($page)) {
            $page = 1;
        }
        $user = null;
        $community = null;
        $author = null;
        $latestPosts = $posts->findLatest($page, $user, $community, $author);
        $data = [];
        foreach ($latestPosts->getResults() as $post) {
            $data[] = $transformer->transform($post);
        }
        return $this->json(
            [
                'status' => 'success',
                'data' => $data
            ]
        );
    }

    /**
     * @Route("/{id}", name="api_post_detail")
     * @param Request $request
     * @return Response
     */
    public function postDetail(Request $request): Response
    {
        return $this->json(
            [
                'status' => 'success',
                'data' => [
                    'community' => new Post(),
                ]
            ]
        );
    }
}
