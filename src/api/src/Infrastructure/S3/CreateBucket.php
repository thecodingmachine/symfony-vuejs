<?php

declare(strict_types=1);

namespace App\Infrastructure\S3;

use Aws\S3\S3MultiRegionClient;

class CreateBucket
{
    protected S3MultiRegionClient $client;

    public function __construct(S3MultiRegionClient $client)
    {
        $this->client = $client;
    }

    public function create(string $bucketName): bool
    {
        $buckets = $this->client->listBuckets();
        foreach ($buckets['Buckets'] as $bucket) {
            if ($bucket['Name'] === $bucketName) {
                // Bucket exists.
                return false;
            }
        }

        $this->client->createBucket(['Bucket' => $bucketName]);

        return true;
    }
}
