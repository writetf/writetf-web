<?php

namespace App\Controller\Admin\English;

use App\Repository\EnglishVideoRepository;
use App\Repository\EnglishCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("admin/english")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="admin_english_index")
     * @param EnglishCategoryRepository $englishCategoryRepository
     * @param EnglishVideoRepository $englishVideoRepository
     * @return Response
     */
    public function index(
        EnglishCategoryRepository $englishCategoryRepository,
        EnglishVideoRepository $englishVideoRepository
    ): Response {
        return $this->render(
            'admin/english/index.html.twig',
            [
                'postsCount' => 1,
                'categoriesCount' => 123,
                'bannersCount' => 123
            ]
        );
    }
}