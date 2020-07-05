<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageUploader
{
    private $targetDirectory;

    private $security;

    private $logger;

    public function __construct($targetDirectory, Security $security, LoggerInterface $logger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->security = $security;
        $this->logger = $logger;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = uniqid() . '.' . $file->guessExtension();

        try {
            /** @var User $user */
            $user = $this->security->getUser();
            $directory = sprintf('%s/upload/images/%s', $this->getTargetDirectory(), $user->getId());
            $file->move($directory, $fileName);
        } catch (FileException $e) {
            $this->logger->error('Can not upload file: ' . json_encode([
                    'message' => $e->getMessage(),
                    'trace' => $e->getTrace()
                ]));
            return false;
        }
        return sprintf('/upload/images/%s/%s', $user->getId(), $fileName);
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
