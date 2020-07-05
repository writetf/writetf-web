<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("settings")
 * @IsGranted("ROLE_USER")
 */
class SettingController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="settings_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('settings/index.html.twig');
    }
}
