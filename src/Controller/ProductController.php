<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 */
class ProductController extends AbstractController
{
    /**
     * @return Response
     */
    public function detail(): Response
    {
        return $this->render('product/index.html.twig');
    }
}
