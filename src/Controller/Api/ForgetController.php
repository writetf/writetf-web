<?php

namespace App\Controller\Api;

use DateTime;
use Exception;
use App\Entity\Forget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("api/v1/forget")
 */
class ForgetController extends ApiController
{
    /**
     * @Route("", methods={"POST"}, name="api_forget_index")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $device = $data['device'];
        $data = $data['data'];
        $forget = new Forget();
        $forget->setData($data);
        $forget->setDevice($device);
        $forget->setCreatedAt(new DateTime());
        $entityManager->persist($forget);
        $entityManager->flush();

        return $this->json(
            [
                'status' => 'success',
            ]
        );
    }
}
