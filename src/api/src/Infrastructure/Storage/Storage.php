<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use League\Flysystem\FilesystemInterface;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Storage
{
    protected ValidatorInterface $validator;
    protected FilesystemInterface $storage;

    public function __construct(ValidatorInterface $validator, FilesystemInterface $storage)
    {
        $this->validator = $validator;
        $this->storage   = $storage;
    }

    /**
     * @param resource $resouce
     */
    protected function put(string $path, $resouce): void
    {
        $result = $this->storage->putStream(
            $path,
            $resouce
        );

        if ($result === true) {
            return;
        }

        throw new RuntimeException(
            'Failed to store "' .
            $path .
            '"'
        );
    }

    protected function remove(string $path): void
    {
        $result = $this->storage->delete($path);

        if ($result !== false) {
            return;
        }

        throw new RuntimeException(
            'Failed to delete "' .
            $path .
            '"'
        );
    }

    protected function has(string $path): bool
    {
        return $this->storage->has($path);
    }
}
