<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * @version    0.1
 * @author     Stefan Krenz <krenz@marmalade.de>
 * @link       http://www.marmalade.de
 */

namespace KyoyaDe\Tragopan\PhpClient\CertificateAuthority;

use GuzzleHttp\Client;
use KyoyaDe\Tragopan\PhpClient\ConfigAwareInterface;
use KyoyaDe\Tragopan\PhpClient\ConfigAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class DownloadCommand extends Command implements ConfigAwareInterface
{
    use ConfigAwareTrait;

    const DEFAULT_CA_FILE = 'cacert.pem';

    protected function configure()
    {
        $this
            ->setName('download:ca')
            ->setDescription('Downloads the CA certificate.')
            ->addOption(
                'output-file',
                'o',
                InputOption::VALUE_REQUIRED,
                'Filename to put the certificate into.',
                static::DEFAULT_CA_FILE
            )
            ->addOption(
                'api-key',
                'a',
                InputOption::VALUE_REQUIRED,
                'API-Key to use for the download.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $apiKey = $this->fetchApiKey($input, $output);

        if (null === $apiKey) {
            $output->writeln("<comment>No API-Key provided, exiting!</comment>");

            return;
        }

        $outputFile = $input->getOption('output-file');
        if (
            in_array($outputFile, [null, static::DEFAULT_CA_FILE], true) &&
            isset($this->config['defaults.ca-cert-file'])
        ) {
            $outputFile = $this->config['defaults.files.ca.cert'];
        }
        $output->writeln("Downloading CA certificate to {$outputFile}.");

        $this->downloadFile($output, $apiKey, $outputFile);

        $output->writeln('');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|string
     */
    protected function fetchApiKey(InputInterface $input, OutputInterface $output)
    {
        $apiKey = $input->getOption('api-key');

        if (null === $apiKey && isset($this->config['api-key'])) {
            $apiKey = $this->config['api-key'];
        }

        if (in_array($apiKey, [null, false], true)) {
            /** @var QuestionHelper $helper */
            $helper   = $this->getHelper('question');
            $question = new Question('Please provide your API-Key: ');
            $apiKey   = $helper->ask($input, $output, $question);
        }

        return $apiKey;
    }

    /**
     * @param OutputInterface $output
     * @param string          $apiKey
     * @param string          $outputFile
     */
    protected function downloadFile(OutputInterface $output, $apiKey, $outputFile)
    {
        $progress = new ProgressBar($output);
        $progress->setBarWidth(80);
        $progress->setFormat('debug');

        $client   = new Client(['base_uri' => $this->config['url']]);
        $client->request(
            'GET',
            '/cert',
            [
                'headers'  => [
                    'X-CA-Api-Key' => $apiKey,
                ],
                'sink'     => $outputFile,
                'progress' => function ($downloadTotal, $downloadedBytes, $uploadTotal, $uploadedBytes) use ($progress
                ) {
                    $progress->start($downloadTotal);
                    $progress->setProgress($downloadedBytes);
                },
            ]
        );
        $progress->finish();
    }
}
