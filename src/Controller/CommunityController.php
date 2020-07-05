<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\CommunityCategory;
use App\Repository\CommunityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommunityCategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class CommunityController extends AbstractController
{
    /**
     * @Route("/w/all", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="community_index")
     * @Cache(smaxage="10")
     *
     * NOTE: For standard formats, Symfony will also automatically choose the best
     * Content-Type header for the response.
     * See https://symfony.com/doc/current/quick_tour/the_controller.html#using-formats
     * @param Request $request
     * @param int $page
     * @param string $_format
     * @param CommunityRepository $communities
     * @return Response
     */
    public function index(Request $request, int $page, string $_format, CommunityCategoryRepository $communityCategoryRepository): Response
    {
        $categories = $communityCategoryRepository->findAll();

        return $this->render('community/index.' . $_format . '.twig', [
            'categories' => $categories,
        ]);
    }
}
