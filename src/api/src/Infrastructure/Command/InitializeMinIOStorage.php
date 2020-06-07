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
    private string $publicBucket;
    private string $privateBucket;
    private string $publicSource;
    private string $privateSource;

    public function __construct(
        S3MultiRegionClient $minioClient,
        ParameterBagInterface $parameters
    ) {
        $this->client        = $minioClient;
        $this->publicBucket  = $parameters->get('app.storage_public_bucket');
        $this->privateBucket = $parameters->get('app.storage_private_bucket');
        $this->publicSource  = $parameters->get('app.storage_public_source');
        $this->privateSource = $parameters->get('app.storage_private_source');

        parent::__construct('app:init-storage:minio');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! $this->areStorageSourcesValid($output)) {
            return Command::FAILURE;
        }

        $publicBucketExists  = $this->storageHas($output, $this->publicBucket);
        $privateBucketExists = $this->storageHas($output, $this->privateBucket);

        if (! $publicBucketExists) {
            $this->createBucket($output, $this->publicBucket);
        }

        if (! $privateBucketExists) {
            $this->createBucket($output, $this->privateBucket, false);
        }

        return Command::SUCCESS;
    }

    private function areStorageSourcesValid(OutputInterface $output): bool
    {
        $template = '%s storage source is not "%s" but "%s"';
        if ($this->publicSource !== 'public.storage.minio') {
            $message = sprintf(
                $template,
                'Public',
                'public.storage.minio',
                $this->publicSource
            );

            $output->writeln($message);

            return false;
        }

        if ($this->privateSource !== 'private.storage.minio') {
            $message = sprintf(
                $template,
                'Private',
                'private.storage.minio',
                $this->privateSource
            );

            $output->writeln($message);

            return false;
        }

        return true;
    }

    private function storageHas(OutputInterface $output, string $bucketName): bool
    {
        $buckets = $this->client->listBuckets();
        foreach ($buckets['Buckets'] as $bucket) {
            if ($bucket['Name'] === $bucketName) {
                $output->writeln(
                    'Bucket "' .
                    $bucketName .
                    '" already exists'
                );

                return true;
            }
        }

        return false;
    }

    private function createBucket(OutputInterface $output, string $bucketName, bool $public = true): void
    {
        $this->client->createBucket(['Bucket' => $bucketName]);

        if ($public === true) {
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
                'Bucket' => $bucketName,
                'Policy' => sprintf(
                    $policyReadOnly,
                    $bucketName,
                    $bucketName
                ),
            ]);
        }

        $output->writeln(
            'Bucket "' .
            $bucketName .
            '" created'
        );
    }
}
