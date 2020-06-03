<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use League\Flysystem\FilesystemInterface;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class Storage
{
    protected ValidatorInterface $validator;
    protected FilesystemInterface $uploadsStorage;

    public function __construct(ValidatorInterface $validator, FilesystemInterface $uploadsStorage)
    {
        $this->validator      = $validator;
        $this->uploadsStorage = $uploadsStorage;
    }

    /**
     * @param resource $resouce
     */
    protected function store(string $path, $resouce) : void
    {
        $result = $this->uploadsStorage->putStream(
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

    protected function delete(string $path) : void
    {
        $result = $this->uploadsStorage->delete($path);

        if ($result !== false) {
            return;
        }

        throw new RuntimeException(
            'Failed to delete "' .
            $path .
            '"'
        );
    }

    protected function exist(string $path) : bool
    {
        return $this->uploadsStorage->has($path);
    }
}
