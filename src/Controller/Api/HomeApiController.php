<?php

namespace App\Controller\Api;

use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/v1")
 */
class HomeApiController extends BaseController
{
    /**
     * @Route("/", name="api_index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->json([
            'status' => 'success',
            'data' => [
                'service' => "Writetf's APIs",
                'version' => 'v1.0'
            ]
        ]);
    }
}
