<?php

namespace App\Controller;

use DateTime;
use App\Entity\DDNS;
use App\Helpers\RequestHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DDNSController extends AbstractController
{
    /**
     * @Route("/ddns/{name}", name="ddns_index")
     * @param string $name
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function ddns(string $name, Request $request, EntityManagerInterface $entityManager): Response
    {
        $ddns = new DDNS();
        $ddns->setName($name);
        $ddns->setIp(RequestHelper::getRemoteIPAddress());
        $ddns->setUserAgent(RequestHelper::getUserAgent());
        $ddns->setCreatedAt(new DateTime());
        $requestArray = [
            'headers' => $request->headers->all(),
            'parameters' => $request->query->all(),
            'content' => json_decode($request->getContent()),
            'uri' => $request->getRequestUri()
        ];
        $ddns->setRequest($requestArray);
        $entityManager->persist($ddns);
        $entityManager->flush();
        return $this->json(
            [
                'status' => 'success',
                'request' => $requestArray
            ]
        );
    }
}
