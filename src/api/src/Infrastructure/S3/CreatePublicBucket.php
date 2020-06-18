<?php

declare(strict_types=1);

namespace App\Infrastructure\S3;

use function Safe\sprintf;

final class CreatePublicBucket extends CreateBucket
{
    public function create(string $bucketName): bool
    {
        $created = parent::create($bucketName);
        if ($created === false) {
            return $created;
        }

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

        return true;
    }
}
