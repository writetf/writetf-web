<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("checkout")
 * @IsGranted("ROLE_USER")
 */
class CheckoutController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="checkout_index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('checkout/index.html.twig');
    }
}
