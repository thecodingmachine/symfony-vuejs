<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class PrivateStorage extends Storage
{
    public function __construct(ValidatorInterface $validator, FilesystemInterface $privateStorage)
    {
        parent::__construct($validator, $privateStorage);
    }
}
