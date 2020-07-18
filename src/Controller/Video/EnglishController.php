<?php

namespace App\Controller\Video;

use App\Entity\Video;
use App\Entity\VideoCategory;
use App\Repository\VideoRepository;
use App\Repository\VideoCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("english")
 */
class EnglishController extends AbstractController
{
    const PRODUCT_NAME = 'english';

    /**
     * @Route("", methods={"GET"}, name="english_home_index")
     * @param VideoCategoryRepository $videoCategoryRepository
     * @param VideoRepository $videoRepository
     * @return Response
     */
    public function index(
        VideoCategoryRepository $videoCategoryRepository,
        VideoRepository $videoRepository
    ): Response {
        return $this->render(
            'english/index.html.twig',
            [
                'categories' => $videoCategoryRepository->findByProductName(static::PRODUCT_NAME),
                'videos' => $videoRepository->findByProductName(static::PRODUCT_NAME)
            ]
        );
    }

    /**
     * @Route("/category/{id}", methods={"GET"}, name="english_category_detail")
     * @param VideoCategory $videoCategory
     * @return Response
     */
    public function englishCategoryDetail(VideoCategory $videoCategory): Response
    {
        return $this->render(
            'english/category.html.twig',
            [
                'englishCategory' => $videoCategory
            ]
        );
    }

    /**
     * @Route("/video/{id}", methods={"GET"}, name="english_video_detail")
     * @param Video $video
     * @return Response
     */
    public function videoDetail(Video $video)
    {
        return $this->render(
            'english/video.html.twig',
            [
                'video' => $video
            ]
        );
    }
}
