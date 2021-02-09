<?php

namespace App\Controller\Writing;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class WritingController
 * @package App\Controller\Writing
 * @Route("writing")
 */
class WritingController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="writing_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('writing/index.html.twig');
    }

    /**
     * @Route("/register", methods={"GET"}, name="writing_register")
     * @return Response
     */
    public function register(): Response
    {
        return $this->render('writing/register.html.twig');
    }

    /**
     * @Route("/login", methods={"GET"}, name="writing_login")
     * @return Response
     */
    public function login(): Response
    {
        return $this->render('writing/login.html.twig');
    }
}
