<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Model\Storable\Storable;
use League\Flysystem\FilesystemInterface;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

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
     * @param Storable[] $storables
     *
     * @return string[]
     */
    protected function putAll(array $storables): array
    {
        $fileNames = [];
        foreach ($storables as $storable) {
            try {
                $fileNames[] = $this->put($storable);
            } catch (Throwable $e) {
                // If any exception occurs, delete
                // already stored pictures.
                $this->deleteAll($fileNames);

                throw $e;
            }
        }

        return $fileNames;
    }

    protected function put(Storable $storable): string
    {
        $fileName = $storable->getGeneratedFileName();
        $path     = $this->getPath($fileName);
        $result   = $this->storage->putStream(
            $path,
            $storable->getResource()
        );

        if ($result === true) {
            return $fileName;
        }

        throw new RuntimeException(
            'Failed to store "' .
            $path .
            '"'
        );
    }

    /**
     * @param string[] $fileNames
     */
    public function deleteAll(array $fileNames): void
    {
        foreach ($fileNames as $fileName) {
            $this->delete($fileName);
        }
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
