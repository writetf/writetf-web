<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("policy")
 */
class PolicyController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="policy_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('policy/index.html.twig');
    }
}
