<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 */
class StoreController extends BaseController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function love(Request $request): Response
    {
        return $this->render('store/index.html.twig');
    }
}
