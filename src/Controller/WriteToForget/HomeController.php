<?php

namespace App\Controller\WriteToForget;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/write-to-forget", methods={"GET"}, name="write_to_forget_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('write-to-forget/index.html.twig');
    }
}
