<?php

namespace App\Controller\Api;

use Exception;
use App\Service\UserService;
use App\Request\LoginRequest;
use App\Service\TokenService;
use App\Request\RegisterRequest;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @Route("api/v1")
 */
class SecurityApiController extends BaseController
{
    /**
     * @Route("/login", name="api_login", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param TokenService $tokenService
     * @return Response
     * @throws Exception
     */
    public function login(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository,
        TokenService $tokenService
    ): Response {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $password = $data['password'];
        $loginRequest = new LoginRequest($username, $password);
        $this->validate($loginRequest);
        $user = $userRepository->findOneBy(
            [
                'username' => $username
            ]
        );
        if (!$user) {
            return $this->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid credentials.'
                ]
            );
        }
        $passwordIsValid = $passwordEncoder->isPasswordValid($user, $password);
        if (!$passwordIsValid) {
            return $this->json(
                [
                    'status' => 'error',
                    'message' => 'Invalid credentials.'
                ]
            );
        }
        $token = $tokenService->createToken($user);
        return $this->json(
            [
                'status' => 'success',
                'data' => [
                    'token' => $token
                ]
            ]
        );
    }

    /**
     * @Route("/register", name="api_register", methods={"POST"})
     * @param Request $request
     * @param UserRepository $users
     * @param UserService $userService
     * @param TokenService $tokenService
     * @return Response
     * @throws Exception
     */
    public function register(
        Request $request,
        UserRepository $users,
        UserService $userService,
        TokenService $tokenService
    ): Response {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $username = $data['username'];
        $password = $data['password'];
        $registerRequest = new RegisterRequest($email, $username, $password);
        $this->validate($registerRequest);
        $existingUser = $users->findOneBy(['username' => $username]);
        $existingEmail = $users->findOneBy(['email' => $email]);
        if ($existingEmail) {
            return $this->json(
                [
                    'status' => 'error',
                    'message' => sprintf('There is already a user registered with the "%s" email.', $email)
                ]
            );
        }
        if ($existingUser) {
            return $this->json(
                [
                    'status' => 'error',
                    'message' => sprintf('There is already a user registered with the "%s" username.', $username)
                ]
            );
        }
        $user = $userService->createUser($email, $username, $password);
        $token = $tokenService->createToken($user);

        return $this->json(
            [
                'status' => 'success',
                'data' => [
                    'token' => $token
                ]
            ]
        );
    }
}
