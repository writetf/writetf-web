<?php

namespace App\Controller;

use App\Form\ShortPostType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 */
class ChatController extends AbstractController
{

    public function chatList(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('blog/_chat.html.twig', [
            'users' => $users,
        ]);
    }
}
