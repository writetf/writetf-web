<?php

namespace App\Service;

use App\Entity\EnglishVideo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EnglishCategoryRepository;

class EnglishVideoService
{
    protected $entityManager;
    protected $englishCategoryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        EnglishCategoryRepository $englishCategoryRepository
    ) {
        $this->entityManager = $entityManager;
        $this->englishCategoryRepository = $englishCategoryRepository;
    }

    public function youtubeCreate(Request $request)
    {
        $name = $request->get('name');
        $categoryId = $request->get('category_id');
        $description = $request->get('description');
        $duration = $request->get('duration');
        $hash = $request->get('hash');
        $englishVideo = new EnglishVideo();
        $englishCategory = $this->englishCategoryRepository->find($categoryId);
        $englishVideo->setEnglishCategory($englishCategory);
        $englishVideo->setType('youtube');
        $englishVideo->setName($name);
        $englishVideo->setDescription($description);
        $englishVideo->setDuration($duration);
        $englishVideo->setUri('https://www.youtube.com/embed/' . $hash);
        $englishVideo->setThumbnail('https://i.ytimg.com/vi/' . $hash . '/maxresdefault.jpg');
        $this->entityManager->persist($englishVideo);
        $this->entityManager->flush();
    }

    /**
     * @param EnglishVideo $englishVideo
     * @param Request $request
     */
    public function update($englishVideo, Request $request)
    {
        $name = $request->get('name');
        $categoryId = $request->get('category_id');
        $description = $request->get('description');
        $duration = $request->get('duration');
        $thumbnail = $request->get('thumbnail');
        $type = $request->get('type');
        $uri = $request->get('uri');
        $englishCategory = $this->englishCategoryRepository->find($categoryId);
        $englishVideo->setEnglishCategory($englishCategory);
        $englishVideo->setType($type);
        $englishVideo->setName($name);
        $englishVideo->setDescription($description);
        $englishVideo->setDuration($duration);
        $englishVideo->setUri($uri);
        $englishVideo->setThumbnail($thumbnail);
        $this->entityManager->persist($englishVideo);
        $this->entityManager->flush();
    }
}
