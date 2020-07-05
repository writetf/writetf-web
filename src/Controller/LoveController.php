<?php

namespace App\Controller;

use Exception;
use App\Entity\Post;
use App\Entity\Love;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/love")
 * @IsGranted("ROLE_USER")
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class LoveController extends AbstractController
{
    /**
     * @Route("/{postId}", methods={"POST"}, name="love_index")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @ParamConverter("post", options={"mapping": {"postId": "id"}})
     * @param Request $request
     * @param Post $post
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     * @throws Exception
     */
    public function love(Request $request, Post $post, EventDispatcherInterface $eventDispatcher): Response
    {
        if ($request->isMethod("GET")) {
            return $this->redirectToRoute('blog_post', ['id' => $post->getId()]);
        }
        $love = new Love();
        $love->setUser($this->getUser());
        $love->setPost($post);
        $love->setCreatedAt(new \DateTime());
        $em = $this->getDoctrine()->getManager();
        $em->persist($love);
        $em->flush();

        return $this->json([
            'status' => 'success'
        ]);
    }
}
