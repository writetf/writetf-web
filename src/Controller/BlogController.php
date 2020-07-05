<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\PostRepository;
use App\Repository\QuoteRepository;
use App\Repository\CommunityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller used to manage blog contents in the public part of the site.
 *
 * @Route("/")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/t/all", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="blog_index")
     * @Route("/t/all/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="blog_index_paginated")
     * @Route("/w/{slug<^((?!all)[a-zA-Z0-9\-\_]+)>}", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="blog_community_index")
     * @Route("/w/{slug<^((?!all)[a-zA-Z0-9\-\_]+)>}/page/{page<[1-9]\d*>}", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="blog_community_index_paginated")
     * @Route("/u/{userSlug<^((?!all)[a-zA-Z0-9\-\_\.]+)>}", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="blog_user_index")
     * @Route("/u/{userSlug<^((?!all)[a-zA-Z0-9\-\_\.]+)>}/page/{page<[1-9]\d*>}", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="blog_user_index_paginated")
     * @Cache(smaxage="10")
     *
     * NOTE: For standard formats, Symfony will also automatically choose the best
     * Content-Type header for the response.
     * See https://symfony.com/doc/current/quick_tour/the_controller.html#using-formats
     * @param Request $request
     * @param int $page
     * @param string $_format
     * @param string|null $slug
     * @param string|null $userSlug
     * @param PostRepository $posts
     * @param CommunityRepository $communities
     * @param UserRepository $users
     * @return Response
     */
    public function index(
        Request $request,
        int $page,
        string $_format,
        string $slug = null,
        string $userSlug = null,
        PostRepository $posts,
        CommunityRepository $communities,
        UserRepository $users
    ): Response
    {
        $user = null;
        if ($userSlug) {
            $user = $users->findOneBy(['slug' => $userSlug]);
        }
        $community = null;
        $coverWidth = 300;
        $coverHeight = 300;
        if ($slug) {
            $community = $communities->findOneBy(['slug' => $slug]);
            if (empty($community)) {
                return $this->redirectToRoute('blog_index');
            } else {
                try {
                    if (!empty($community->getCover())) {
                        list($coverWidth, $coverHeight) = getimagesize($community->getCover());
                    }
                } catch (\Exception $exception) {
                    $coverWidth = 300;
                    $coverHeight = 300;
                }
            }
        }

        $author = $this->getUser();

        $latestPosts = $posts->findLatest($page, $user, $community, $author);

        // Every template name also has two extensions that specify the format and
        // engine for that template.
        // See https://symfony.com/doc/current/templating.html#template-suffix
        return $this->render('blog/index.' . $_format . '.twig', [
            'coverWidth' => $coverWidth,
            'coverHeight' => $coverHeight,
            'paginator' => $latestPosts,
            'community' => $community,
            'communities' => $communities->getTop(),
            'user' => $user
        ]);
    }
}
