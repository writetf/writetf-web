<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\View;
use App\Entity\Community;
use App\Form\ShortPostType;
use App\Helpers\ImageHelper;
use App\Helpers\RequestHelper;
use HtmlSanitizer\SanitizerInterface;
use App\Repository\CommentRepository;
use App\Repository\CommunityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class PostController
 * @package App\Controller
 */
class PostController extends AbstractController
{
    /**
     * @Route("/posts", methods={"POST"}, name="post_new")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param CommunityRepository $communities
     * @param SanitizerInterface $sanitizer
     * @return Response
     */
    public function postNew(Request $request, CommunityRepository $communities, SanitizerInterface $sanitizer): Response
    {
        $post = new Post();
        /** @var User $user */
        $user = $this->getUser();
        $post->setAuthor($user);
        $form = $this->createForm(ShortPostType::class, $post);
        $communityId = $request->get('communityId') ?? null;
        if (!empty($communityId)) {
            $community = $communities->find($communityId);
            if ($community) {
                $post->setCommunity($community);
            }
        }
        $form->handleRequest($request);
        if (!empty($post->getTitle())) {
            $post->setTitle($sanitizer->sanitize($post->getTitle()));
        }
        if (!empty($post->getContent())) {
            $post->setContent($sanitizer->sanitize($post->getContent()));
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('blog_post', ['id' => $post->getId()]);
        }
        return $this->redirectToRoute('blog_index');
    }


    /**
     * @Route("/t/{id}", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="blog_post")
     * @Route("/t/{id}/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="blog_post_paginated")
     *
     * NOTE: The $post controller argument is automatically injected by Symfony
     * after performing a database query looking for a Post with the 'slug'
     * value given in the route.
     * See https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
     * @param Request $request
     * @param Post $post
     * @param int $page
     * @param CommentRepository $comments
     * @param EntityManagerInterface $entityManager
     * @param CommunityRepository $communities
     * @return Response
     */
    public function postShow(
        Request $request,
        Post $post,
        int $page,
        CommentRepository $comments,
        EntityManagerInterface $entityManager,
        CommunityRepository $communities
    ): Response {
        $this->viewTracking($post, $entityManager);
        $latestComments = $comments->findLatest($page, $post);
        $thumbnailWidth = 300;
        $thumbnailHeight = 300;
        if (!empty($post->getThumbnail())) {
            $imageSize = ImageHelper::getSize($request->getUriForPath($post->getThumbnail()));
            $thumbnailWidth = $imageSize['width'];
            $thumbnailHeight = $imageSize['height'];
        }
        return $this->render(
            'blog/post_show.html.twig',
            [
                'post' => $post,
                'comments' => $latestComments->getResults(),
                'thumbnailWidth' => $thumbnailWidth,
                'thumbnailHeight' => $thumbnailHeight,
                'tagName' => null,
                'communities' => $communities->getTop(),
            ]
        );
    }

    /**
     * @Route("/posts/t/{id}", name="blog_post_redirect")
     * @Route("/posts/t/{id}/page/{page<[1-9]\d*>}", name="blog_post_redirect_paginated")
     * @param $id
     * @return RedirectResponse
     */
    public function postShowRedirect($id)
    {
        return $this->redirectToRoute(
            'blog_post',
            [
                'id' => $id
            ],
            Response::HTTP_MOVED_PERMANENTLY
        );
    }

    /**
     * @param $post
     * @param EntityManagerInterface $entityManager
     */
    protected function viewTracking($post, $entityManager)
    {
        $view = new View();
        /** @var User $user */
        $user = $this->getUser();
        if ($user) {
            $view->setUser($user);
        }
        $view->setCountry(RequestHelper::getCountry());
        $view->setUserAgent(RequestHelper::getUserAgent());
        $view->setIpAddress(RequestHelper::getRemoteIPAddress());
        $view->setPost($post);
        $entityManager->persist($view);
        $entityManager->flush();
    }

    public function postForm($community): Response
    {
        $form = $this->createForm(ShortPostType::class);
        $communityId = null;
        if (!empty($community) && $community instanceof Community) {
            $communityId = $community->getId();
        }

        return $this->render(
            'blog/_create_new_post.html.twig',
            [
                'form' => $form->createView(),
                'communityId' => $communityId
            ]
        );
    }
}
