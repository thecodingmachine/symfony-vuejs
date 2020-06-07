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

    abstract protected function getDirectoryName(): string;

    private function getPath(string $fileName): string
    {
        return $this->getDirectoryName() . '/' . $fileName;
    }

    /**
     * @param resource $resouce
     */
    protected function put(string $fileName, $resouce): void
    {
        $path   = $this->getPath($fileName);
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

    public function delete(string $fileName): void
    {
        $path   = $this->getPath($fileName);
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

    public function fileExists(string $fileName): bool
    {
        $path = $this->getPath($fileName);

        return $this->storage->has($path);
    }
}
