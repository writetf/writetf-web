<?php

namespace App\Service;

use Google_Client;
use App\Entity\Video;
use Google_Service_YouTube;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VideoCategoryRepository;
use Symfony\Component\HttpFoundation\Request;

class VideoService
{
    protected $entityManager;
    protected $videoCategoryRepository;
    protected $videoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        VideoRepository $videoRepository,
        VideoCategoryRepository $videoCategoryRepository,
        Google_Client $googleClient
    ) {
        $this->entityManager = $entityManager;
        $this->videoCategoryRepository = $videoCategoryRepository;
        $this->videoRepository = $videoRepository;
    }

    public function youtubeCreate(Request $request)
    {
        $name = $request->get('name');
        $categoryId = $request->get('category_id');
        $description = $request->get('description');
        $duration = $request->get('duration');
        $hash = $request->get('hash');
        $video = new Video();
        $video->setYoutubeId($hash);
        $videoCategory = $this->videoCategoryRepository->find($categoryId);
        $video->setVideoCategory($videoCategory);
        $video->setType('youtube');
        $video->setName($name);
        $video->setDescription($description);
        $video->setDuration($duration);
        $video->setUri('https://www.youtube.com/embed/' . $hash);
        $video->setThumbnail('https://i.ytimg.com/vi/' . $hash . '/maxresdefault.jpg');
        $this->entityManager->persist($video);
        $this->entityManager->flush();
    }

    /**
     * @param Video $video
     * @param Request $request
     */
    public function update($video, Request $request)
    {
        $name = $request->get('name');
        $categoryId = $request->get('category_id');
        $description = $request->get('description');
        $duration = $request->get('duration');
        $thumbnail = $request->get('thumbnail');
        $type = $request->get('type');
        $uri = $request->get('uri');
        $videoCategory = $this->videoCategoryRepository->find($categoryId);
        $video->setVideoCategory($videoCategory);
        $video->setType($type);
        $video->setName($name);
        $video->setDescription($description);
        $video->setDuration($duration);
        $video->setUri($uri);
        $video->setThumbnail($thumbnail);
        $this->entityManager->persist($video);
        $this->entityManager->flush();
    }
}
