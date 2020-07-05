<?php

namespace App\Controller\Api;

use Exception;
use App\Entity\User;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("api/v1/users")
 */
class UserApiController extends BaseController
{

    /**
     * @Route("/me", name="api_me")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function me(Request $request): Response
    {
        $user = new User();
        $user->setId('kdmxaxiyjmo');
        $user->setUsername('ga9xvn');
        $user->setSlug('ga9xvn.buu2x');
        $user->setEmail('ga9xvn@gmail.com');
        $user->setRoles(["ROLE_USER"]);
        return $this->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'slug' => $user->getSlug(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles()
                ]
            ]
        ]);
    }
}
