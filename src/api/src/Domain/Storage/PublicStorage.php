<?php

declare(strict_types=1);

namespace App\Domain\Storage;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class PublicStorage extends Storage
{
    public function __construct(ValidatorInterface $validator, FilesystemInterface $publicStorage)
    {
        parent::__construct($validator, $publicStorage);
    }
}
