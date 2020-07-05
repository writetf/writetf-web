<?php

namespace App\Controller\English;

use App\Entity\EnglishVideo;
use App\Entity\EnglishCategory;
use App\Repository\EnglishVideoRepository;
use App\Repository\EnglishCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("english")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="english_home_index")
     * @param EnglishCategoryRepository $englishCategoryRepository
     * @param EnglishVideoRepository $englishVideoRepository
     * @return Response
     */
    public function index(
        EnglishCategoryRepository $englishCategoryRepository,
        EnglishVideoRepository $englishVideoRepository
    ): Response {
        return $this->render(
            'english/index.html.twig',
            [
                'categories' => $englishCategoryRepository->findAll(),
                'videos' => $englishVideoRepository->findBy(
                    [],
                    [
                        'createdAt' => 'DESC'
                    ],
                    12
                )
            ]
        );
    }

    /**
     * @Route("/category/{id}", methods={"GET"}, name="english_category_detail")
     * @param EnglishCategory $englishCategory
     * @return Response
     */
    public function englishCategoryDetail(EnglishCategory $englishCategory): Response
    {
        return $this->render(
            'english/category.html.twig',
            [
                'englishCategory' => $englishCategory
            ]
        );
    }

    /**
     * @Route("/video/{id}", methods={"GET"}, name="english_video_detail")
     * @param EnglishVideo $englishVideo
     * @return Response
     */
    public function videoDetail(EnglishVideo $englishVideo)
    {
        return $this->render(
            'english/video.html.twig',
            [
                'video' => $englishVideo
            ]
        );
    }
}
