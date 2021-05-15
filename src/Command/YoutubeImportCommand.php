<?php

namespace App\Command;

use Exception;
use App\Entity\VideoCategory;
use App\Service\YoutubeImportService;
use App\Repository\VideoCategoryRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class YoutubeImportCommand extends Command
{
    protected static $defaultName = 'youtube-import';
    protected $youtubeImportService;
    protected $videoCategoryRepository;

    public function __construct(
        YoutubeImportService $youtubeImportService,
        VideoCategoryRepository $videoCategoryRepository,
        string $name = null
    ) {
        parent::__construct($name);
        $this->youtubeImportService = $youtubeImportService;
        $this->videoCategoryRepository = $videoCategoryRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import youtube by search')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $categories = $this->videoCategoryRepository->findAll();
        /** @var VideoCategory $category */
        foreach ($categories as $category) {
            $io->success(
                'Getting data for keyword: ' . $category->getKeyword()
            );
            $data = $this->youtubeImportService->search($category->getKeyword());
            $io->success(
                'Successfully get items from Youtube for keyword: ' . $category->getKeyword() . ' - Total:' . count($data)
            );
            $io->success(
                'Importing data to database for keyword: ' . $category->getKeyword() . ' - Category ID: ' . $category->getId()
            );
            $this->youtubeImportService->createByYoutubeData($data, $category->getId());
            $io->success(
                'Successfully import data to database for keyword: ' . $category->getKeyword() . ' - Category ID: ' . $category->getId()
            );
            sleep(5);
        }
        $io->success('Successfully import data from Youtube!');

        return true;
    }
}
