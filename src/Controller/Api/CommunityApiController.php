<?php

namespace App\Controller\Api;

use App\Entity\Community;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("api/v1/communities")
 */
class CommunityApiController extends BaseController
{
    /**
     * @Route("/", name="api_communities_index")
     * @param Request $request
     * @return Response
     */
    public function communities(Request $request): Response
    {
        return $this->json([
            'status' => 'success',
            'data' => [
                new Community(),
                new Community(),
                new Community()
            ]
        ]);
    }

    /**
     * @Route("/{id}", name="api_community_detail")
     * @param Request $request
     * @return Response
     */
    public function communityDetail(Request $request): Response
    {
        return $this->json([
            'status' => 'success',
            'data' => [
                'community' => new Community(),
            ]
        ]);
    }
}
