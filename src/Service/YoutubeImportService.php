<?php

namespace App\Service;

use DateTime;
use Exception;
use DateInterval;
use Google_Client;
use App\Entity\Video;
use Google_Service_YouTube;
use Psr\Log\LoggerInterface;
use Google_Service_YouTube_Video;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Google_Service_YouTube_SearchResult;
use App\Repository\VideoCategoryRepository;

class YoutubeImportService
{
    const TOTAL_ITEMS = 36;
    const LIMIT = 12; // max 50

    protected $entityManager;
    protected $logger;
    protected $videoCategoryRepository;
    protected $youtubeService;
    protected $videoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        VideoRepository $videoRepository,
        VideoCategoryRepository $videoCategoryRepository,
        Google_Client $googleClient
    ) {
        $this->entityManager = $entityManager;
        $this->videoCategoryRepository = $videoCategoryRepository;
        $this->videoRepository = $videoRepository;
        $this->youtubeService = new Google_Service_YouTube($googleClient);
        $this->logger = $logger;
    }

    /**
     * @param $string
     * @return array
     * @throws Exception
     */
    public function search($string)
    {
        $nextPageToken = null;
        $data = [];
        $this->logger->info(
            'Getting data for keyword: ' . $string
        );
        for ($i = 0; $i < (self::TOTAL_ITEMS / self::LIMIT); $i++) {
            $videoResults = $this->youtubeService->search->listSearch(
                'snippet',
                [
                    'q' => $string,
                    'type' => 'video',
                    'maxResults' => self::LIMIT,
                    'pageToken' => $nextPageToken
                ]
            );
            $data = array_merge($data, $this->processSearchItems($videoResults->getItems()));
            $nextPageToken = $videoResults->getNextPageToken();
        }
        $this->logger->info(
            'Successfully get items from Youtube for keyword: ' . $string . ' Total:' . count($data)
        );

        return $data;
    }

    /**
     * @param $videoItems
     * @return array|mixed
     * @throws Exception
     */
    protected function processSearchItems($videoItems)
    {
        $data = [];
        /** @var Google_Service_YouTube_SearchResult $videoItem */
        foreach ($videoItems as $videoItem) {
            $id = $videoItem->getId();
            $videoSnippet = $videoItem->getSnippet();
            $thumbnails = $videoSnippet->getThumbnails();
            $videoTitle = $videoSnippet->getTitle();
            $videoDescription = $videoSnippet->getDescription();
            $data[$id->getVideoId()] = [
                'id' => $id->getVideoId(),
                'name' => htmlspecialchars_decode($videoTitle,ENT_QUOTES),
                'description' => htmlspecialchars_decode($videoDescription,ENT_QUOTES),
                'thumbnails' => [
                    'url' => $thumbnails->getHigh()->getUrl(),
                    'width' => $thumbnails->getHigh()->getWidth(),
                    'height' => $thumbnails->getHigh()->getHeight(),
                ]
            ];
        }
        $data = $this->getVideoDuration($data);

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function getVideoDuration($data)
    {
        $keys = array_keys($data);
        $ids = implode(',', $keys);
        $results = $this->youtubeService->videos->listVideos(
            'contentDetails',
            [
                'id' => $ids
            ]
        );
        $items = $results->getItems();
        /** @var Google_Service_YouTube_Video $item */
        foreach ($items as $item) {
            $duration = $item->getContentDetails()->getDuration();
            $data[$item->getId()]['duration'] = $this->youtubeDurationToTime($duration);
        }

        return $data;
    }

    /**
     * @param $youtube_time
     * @return false|string
     * @throws Exception
     */
    protected function youtubeDurationToTime($youtube_time)
    {
        $start = new DateTime('@0'); // Unix epoch
        $start->add(new DateInterval($youtube_time));
        $time = $start->format('H:i:s');
        if (substr($time, 0, 3) === "00:") {
            return substr($time, 3);
        }
        return $time;
    }

    public function createByYoutubeData($data, $categoryId)
    {
        foreach ($data as $item) {
            $this->logger->info(
                'Importing video ID' . $item['id'] . ' Name: ' . $item['name'] . ' Duration: ' . $item['duration']
            );
            $video = $this->videoRepository->findOneBy(
                [
                    'youtubeId' => $item['id']
                ]
            );
            if (!$video) {
                $video = new Video();
                $video->setYoutubeId($item['id']);
            }
            $videoCategory = $this->videoCategoryRepository->find($categoryId);
            $video->setVideoCategory($videoCategory);
            $video->setType('youtube');
            $video->setName($item['name']);
            $video->setDescription($item['description']);
            $video->setDuration($item['duration']);
            $video->setUri('https://www.youtube.com/embed/' . $item['id']);
            $video->setThumbnail($item['thumbnails']['url']);
            $this->entityManager->persist($video);
            $this->entityManager->flush();
        }

        return true;
    }
}
