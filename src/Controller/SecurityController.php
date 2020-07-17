<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Form\RegisterType;
use App\Service\UserService;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class SecurityController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @Route("/login", name="security_login")
     * @Route("/english/login", name="security_english_login")
     * @param Request $request
     * @param Security $security
     * @param AuthenticationUtils $helper
     * @return Response
     */
    public function login(Request $request, Security $security, AuthenticationUtils $helper): Response
    {
        if ($request->getPathInfo() === '/english/login') {
            if ($security->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('english_home_index');
            }
            return $this->render(
                'security/english/login.html.twig',
                [
                    'last_username' => $helper->getLastUsername(),
                    'error' => $helper->getLastAuthenticationError(),
                ]
            );
        }

        if ($security->isGranted('ROLE_USER')) {
            $redirectPath = $request->request->get('_target_path');
            if (!empty($redirectPath)) {
                return $this->redirect($redirectPath);
            }
            return $this->redirectToRoute('community_index');
        }

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $helper->getLastUsername(),
                'error' => $helper->getLastAuthenticationError(),
            ]
        );
    }

    /**
     * @Route("/register", name="security_register")
     * @param Request $request
     * @param UserService $userService
     * @param UserRepository $users
     * @param Security $security
     * @return Response
     * @throws Exception
     */
    public function register(
        Request $request,
        UserService $userService,
        UserRepository $users,
        Security $security
    ): Response {
        if ($security->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('community_index');
        }
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render(
                'security/register.html.twig',
                [
                    'form' => $form->createView(),
                ]
            );
        }
        $existingUser = $users->findOneBy(['username' => $user->getUsername()]);
        $existingEmail = $users->findOneBy(['email' => $user->getEmail()]);
        if ($existingUser) {
            $form->get('username')->addError(
                new FormError(
                    sprintf(
                        'There is already a user registered with the "%s" username.',
                        $user->getUsername()
                    )
                )
            );
            return $this->render(
                'security/register.html.twig',
                [
                    'form' => $form->createView(),
                ]
            );
        }
        if ($existingEmail) {
            $form->get('email')->addError(
                new FormError(
                    sprintf(
                        'There is already a user registered with the "%s" email.',
                        $user->getEmail()
                    )
                )
            );
            return $this->render(
                'security/register.html.twig',
                [
                    'form' => $form->createView(),
                ]
            );
        }
        $email = $user->getEmail();
        $username = $user->getUsername();
        $password = $user->getPassword();
        $user = $userService->createUser($email, $username, $password);

        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->container->get('security.token_storage')->setToken($token);
        $this->container->get('session')->set('_security_main', serialize($token));

        $redirectPath = $request->request->get('_target_path');
        if (!empty($redirectPath)) {
            return $this->redirect($redirectPath);
        }
        return $this->redirectToRoute('community_index');
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in config/packages/security.yaml
     *
     * @Route("/logout", name="security_logout", methods={"POST"})
     */
    public function logout(): void
    {
        throw new Exception('This should never be reached!');
    }
}
