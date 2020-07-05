<?php

namespace App\Controller\Api;

use Exception;
use App\Entity\Post;
use App\Entity\Love;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("api/v1/love")
 */
class LoveController extends ApiController
{
    /**
     * @Route("/{postId}", methods={"POST"}, name="api_love_index")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @ParamConverter("post", options={"mapping": {"postId": "id"}})
     * @param Request $request
     * @param Post $post
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     * @throws Exception
     * @throws \Exception
     */
    public function love(Request $request, Post $post, EventDispatcherInterface $eventDispatcher): Response
    {
        $love = new Love();
        $love->setUser($this->getUser());
        $love->setPost($post);
        $love->setCreatedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($love);
        $em->flush();

        return $this->json(
            [
                'status' => 'success'
            ]
        );
    }
}
