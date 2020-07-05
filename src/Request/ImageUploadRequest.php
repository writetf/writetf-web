<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadRequest
{
    /**
     * @Assert\NotBlank
     * @Assert\File(
     *     maxSize = "8M",
     *     mimeTypes = {
     *         "image/*"
     *     },
     * )
     * @var UploadedFile
     */
    protected $image;

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }
}
