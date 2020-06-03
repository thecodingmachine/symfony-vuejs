<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use Aws\S3\S3MultiRegionClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use function Safe\sprintf;

final class InitializeMinIOStorage extends Command
{
    private S3MultiRegionClient $client;
    private string $bucketName;
    private string $storageSource;

    public function __construct(
        S3MultiRegionClient $minioClient,
        ParameterBagInterface $parameters
    ) {
        $this->client        = $minioClient;
        $this->bucketName    = $parameters->get('app.storage_bucket');
        $this->storageSource = $parameters->get('app.storage_source');

        parent::__construct('app:init-storage:minio');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        if ($this->storageSource !== 'uploads.storage.minio') {
            $output->writeln(
                'Storage source is not "uploads.storage.minio" but "' .
                $this->storageSource .
                '"'
            );

            return Command::FAILURE;
        }

        $buckets = $this->client->listBuckets();
        foreach ($buckets['Buckets'] as $bucket) {
            if ($bucket['Name'] === $this->bucketName) {
                $output->writeln(
                    'Bucket "' .
                    $this->bucketName .
                    '" already exists'
                );

                return Command::SUCCESS;
            }
        }

        $this->client->createBucket([
            'Bucket' => $this->bucketName,
        ]);

        $policyReadOnly = '{
          "Version": "2012-10-17",
          "Statement": [
            {
              "Action": [
                "s3:GetBucketLocation",
                "s3:ListBucket"
              ],
              "Effect": "Allow",
              "Principal": {
                "AWS": [
                  "*"
                ]
              },
              "Resource": [
                "arn:aws:s3:::%s"
              ],
              "Sid": ""
            },
            {
              "Action": [
                "s3:GetObject"
              ],
              "Effect": "Allow",
              "Principal": {
                "AWS": [
                  "*"
                ]
              },
              "Resource": [
                "arn:aws:s3:::%s/*"
              ],
              "Sid": ""
            }
          ]
        }
        ';

        $this->client->putBucketPolicy([
            'Bucket' => $this->bucketName,
            'Policy' => sprintf(
                $policyReadOnly,
                $this->bucketName,
                $this->bucketName
            ),
        ]);

        $output->writeln(
            'Bucket "' .
            $this->bucketName .
            '" created'
        );

        return Command::SUCCESS;
    }
}
