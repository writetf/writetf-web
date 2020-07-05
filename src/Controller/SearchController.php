<?php

namespace App\Controller;

use App\Utils\Markdown;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    /**
     * @param Request $request
     * @param PostRepository $posts
     * @param Markdown $parser
     * @return Response
     */
    public function search(Request $request, PostRepository $posts, Markdown $parser): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->render('blog/search.html.twig');
        }

        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);
        $foundPosts = $posts->findBySearchQuery($query, $limit);

        $results = [];
        foreach ($foundPosts as $post) {
            $results[] = [
                'title' => htmlspecialchars($post->getTitle(), ENT_COMPAT | ENT_HTML5),
                'date' => $post->getPublishedAt()->format('M d, Y'),
                'author' => htmlspecialchars($post->getAuthor()->getDisplayText(), ENT_COMPAT | ENT_HTML5),
                'content' => !empty($post->getContent()) ? $parser->toHtml($post->getContent()) : '',
                'url' => $this->generateUrl('blog_post', ['id' => $post->getId()]),
            ];
        }

        return $this->json($results);
    }
}
