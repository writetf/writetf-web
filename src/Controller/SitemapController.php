<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\PostRepository;
use App\Repository\CommunityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SitemapController extends AbstractController
{
    /**
     * @Route("/sitemap.xml", methods={"GET"}, name="sitemap_index")
     * @param Request $request
     * @return Response
     */
    public function sitemap(
        UrlGeneratorInterface $router,
        Request $request,
        UserRepository $userRepository,
        PostRepository $postRepository,
        CommunityRepository $communityRepository
    ): Response {
        $users = $userRepository->findAll();
        $posts = $postRepository->findAll();
        $communities = $communityRepository->findAll();
        $results = [];
        $lastMod = date('c', time());
        $results[] = [
            'loc' => $request->getSchemeAndHttpHost(),
            'lastmod' => $lastMod,
            'priority' => '1.00'
        ];
        foreach ($communities as $community) {
            $results[] = [
                'loc' => $request->getSchemeAndHttpHost() . $router->generate(
                        'blog_community_index',
                        ['slug' => $community->getSlug()]
                    ),
                'lastmod' => $lastMod,
                'priority' => '0.80'
            ];
        }
        foreach ($users as $user) {
            $results[] = [
                'loc' => $request->getSchemeAndHttpHost() . $router->generate(
                        'blog_user_index',
                        ['userSlug' => $user->getSlug()]
                    ),
                'lastmod' => $lastMod,
                'priority' => '0.80'
            ];
        }
        foreach ($posts as $post) {
            $results[] = [
                'loc' => $request->getSchemeAndHttpHost() . $router->generate(
                        'blog_post',
                        ['id' => $post->getId()]
                    ),
                'lastmod' => date_format($post->getLatestActivityAt(), 'c'),
                'priority' => '0.64'
            ];
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        return $this->render(
            'sitemap/index.xml.twig',
            [
                'results' => $results,
            ],
            $response
        );
    }
}
