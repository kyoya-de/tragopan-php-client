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

namespace KyoyaDe\Tragopan\PhpClient\ServerCertificate;

use GuzzleHttp\Client;
use itguy614\Support\DotArray;
use JMS\Serializer\SerializerBuilder;
use KyoyaDe\Tragopan\PhpClient\ConfigAwareInterface;
use KyoyaDe\Tragopan\PhpClient\ConfigAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommand extends Command implements ConfigAwareInterface
{
    use ConfigAwareTrait;

    protected function configure()
    {
        $this->setName('download:server')->setDescription(
            'Issues a new certificate and download the cert and key file.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $defaults      = DotArray::newDotArray($this->config['defaults']);
        $requestParams = [
            'cert' => $defaults['cert'],
            'host' => $defaults['host'],
        ];

        $client       = new Client(['base_uri' => $this->config['url']]);
        $response     = $client->request('POST', '/cert', ['json' => $requestParams]);
        $responseBody = $response->getBody()->getContents();

        $serializer = SerializerBuilder::create()->build();
        $urls       = DotArray::newDotArray($serializer->deserialize($responseBody, 'array', 'json'));

        $keyFile = $defaults['files.server.key'];
        $output->writeln("Downloading server certificate to {$keyFile}");

        $progress = new ProgressBar($output);
        $progress->setBarWidth(80);
        $progress->setFormat('debug');

        $client->request(
            'GET',
            $urls['key'],
            [
                'sink'     => $keyFile,
                'progress' => function ($downloadTotal, $downloadedBytes, $uploadTotal, $uploadedBytes) use ($progress
                ) {
                    $progress->start($downloadTotal);
                    $progress->setProgress($downloadedBytes);
                },
            ]
        );
        $progress->finish();

        $certFile = $defaults['files.server.cert'];
        $output->writeln("\nDownloading server certificate to {$certFile}");
        $client->request(
            'GET',
            $urls['cert'],
            [
                'sink'     => $certFile,
                'progress' => function ($downloadTotal, $downloadedBytes, $uploadTotal, $uploadedBytes) use ($progress
                ) {
                    $progress->start($downloadTotal);
                    $progress->setProgress($downloadedBytes);
                },
            ]
        );
        $progress->finish();
        $output->writeln("");
    }

}
