<?php

namespace App\Controller\Admin\English;

use App\Entity\Video;
use App\Service\VideoService;
use App\Repository\VideoRepository;
use App\Repository\VideoCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("admin/english")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="admin_english_index")
     * @param VideoRepository $englishVideoRepository
     * @return Response
     */
    public function index(
        VideoRepository $englishVideoRepository
    ): Response {
        $posts = $englishVideoRepository->findBy(
            [],
            [
                'createdAt' => 'DESC'
            ]
        );
        return $this->render(
            'admin/english/index.html.twig',
            [
                'posts' => $posts
            ]
        );
    }

    /**
     * @Route("/create/youtube", name="admin_english_create_youtube")
     * @param Request $request
     * @param VideoCategoryRepository $categoryService
     * @param VideoService $videoService
     * @return RedirectResponse|Response
     */
    public function youtubeCreate(
        Request $request,
        VideoCategoryRepository $categoryService,
        VideoService $videoService
    ) {
        $categories = $categoryService->findAll();
        if ($request->isMethod('post')) {
            $videoService->youtubeCreate($request);
            $this->addFlash('success', 'Create new post successfully!');
            return $this->redirectToRoute('admin_english_index');
        }
        return $this->render(
            'admin/english/youtube_create.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * @Route("/update/{id}", name="admin_english_update")
     * @param $id
     * @param Request $request
     * @param VideoRepository $englishVideoRepository
     * @param VideoService $postService
     * @param VideoCategoryRepository $categoryService
     * @return RedirectResponse|Response
     */
    public function update(
        $id,
        Request $request,
        VideoRepository $englishVideoRepository,
        VideoService $postService,
        VideoCategoryRepository $categoryService
    ) {
        /** @var Video $post */
        $post = $englishVideoRepository->find($id);
        $categories = $categoryService->findAll();
        if (empty($post)) {
            return $this->redirectToRoute('admin_english_index');
        }
        if ($request->isMethod('post')) {
            $postService->update($post, $request);
            $this->addFlash('success', 'Update post successfully!');
            return $this->redirectToRoute('admin_english_index');
        }
        return $this->render(
            'admin/english/update.html.twig',
            [
                'post' => $post,
                'categories' => $categories
            ]
        );
    }
}
