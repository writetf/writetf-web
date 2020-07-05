<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("notifications")
 * @IsGranted("ROLE_USER")
 */
class NotificationController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="admin_notifications_index")
     * @param NotificationRepository $notificationRepository
     * @return Response
     */
    public function index(NotificationRepository $notificationRepository): Response
    {
        $notifications = $notificationRepository->findBy(
            [
                'user' => $this->getUser()
            ],
            [
                'createdAt' => 'DESC'
            ]
        );
        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications
        ]);
    }
}
