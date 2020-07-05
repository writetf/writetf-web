<?php

namespace App\Controller\Home;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="home_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
