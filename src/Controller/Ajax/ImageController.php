<?php

namespace App\Controller\Ajax;

use App\Service\ImageUploader;
use App\Controller\BaseController;
use App\Request\ImageUploadRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 *
 * @Route("/ajax/image")
 * @IsGranted("ROLE_USER")
 */
class ImageController extends BaseController
{
    /**
     * @Route("/upload", methods={"POST"}, name="imgage_upload")
     * @param Request $request
     * @param ImageUploader $imageUploader
     * @return Response
     */
    public function upload(Request $request, ImageUploader $imageUploader): Response
    {
        $upload = new ImageUploadRequest();
        $upload->setImage($request->files->get('image'));
        $this->validate($upload);
        $path = $imageUploader->upload($upload->getImage());
        if (!$path) {
            return new JsonResponse([
                'status' => 'error',
            ], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'status' => 'success',
            'path' => $path
        ]);
    }
}
