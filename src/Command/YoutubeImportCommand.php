<?php

namespace App\Command;

use Exception;
use App\Service\YoutubeImportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class YoutubeImportCommand extends Command
{
    protected static $defaultName = 'youtube-import';
    protected $youtubeImportService;

    public function __construct(YoutubeImportService $youtubeImportService, string $name = null)
    {
        parent::__construct($name);
        $this->youtubeImportService = $youtubeImportService;
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
        $mappings = [
            [
                'q' => 'VOA Learning English',
                'category_id' => 1
            ],
            [
                'q' => 'BBC Learning English',
                'category_id' => 2
            ],
            [
                'q' => 'British Council Learning English',
                'category_id' => 3
            ]
        ];
        foreach ($mappings as $search) {
            $io->success(
                'Getting data for keyword: ' . $search['q']
            );
            $data = $this->youtubeImportService->search($search['q']);
            $io->success(
                'Successfully get items from Youtube for keyword: ' . $search['q'] . ' - Total:' . count($data)
            );
            $io->success('Importing data to database for keyword: '. $search['q'] . ' - Category ID: ' . $search['category_id']);
            $this->youtubeImportService->createByYoutubeData($data, $search['category_id']);
            $io->success('Successfully import data to database for keyword: '. $search['q'] . ' - Category ID: ' . $search['category_id']);
        }
        $io->success('Successfully import data from Youtube!');

        return 0;
    }
}
