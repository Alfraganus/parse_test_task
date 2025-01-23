<?php

namespace App\Console;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCsvAds extends Command
{
    private Client $client;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }


    protected function configure(): void
    {
        $this
            ->setName('import-csv-ads')
            ->setDescription('Import CSV Ads')
            ->addArgument('file')
            ->addArgument('fileProcessingMode', InputArgument::OPTIONAL, 'Mode: multiple insertions, false for single insertion', true);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = $input->getArgument('file');
        $fileProcessingMode = filter_var($input->getArgument('fileProcessingMode'), FILTER_VALIDATE_BOOLEAN);

        if (!$filename || !file_exists($filename)) {
            $output->writeln('<error>File not found or not provided.</error>');
            return Command::FAILURE;
        }

        try {
            $output->writeln('file process :'.$fileProcessingMode);

            $res = $this->sendPostRequest($filename, $fileProcessingMode);
            $output->writeln($res->getBody()->getContents());

            return $res->getStatusCode() === 200 ? Command::SUCCESS : Command::FAILURE;
        } catch (RequestException $e) {
            $output->writeln('<error>Error during API request: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    private function sendPostRequest(string $filename, bool $fileProcessingMode)
    {

        $url = rtrim(env('HOST'), '/') . '/api/import-ads-data';

        return $this->client->post($url, [
            'multipart' => [
                [
                    'name' => 'xlsx',
                    'contents' => \GuzzleHttp\Psr7\Utils::tryFopen($filename, 'r'),
                ],
                [
                    'name'     => 'fileProcessingMode',
                    'contents' => $fileProcessingMode ? 'true' : 'false',
                ],
            ],
            'timeout' => 30,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);
    }
}
