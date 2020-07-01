<?php

declare(strict_types=1);

namespace App\Domain\Storage;

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

    private function getPath(string $filename): string
    {
        return $this->getDirectoryName() . '/' . $filename;
    }

    /**
     * @param Storable[] $storables
     *
     * @return string[]
     */
    protected function putAll(array $storables): array
    {
        $filenames = [];
        foreach ($storables as $storable) {
            try {
                $filenames[] = $this->put($storable);
            } catch (Throwable $e) {
                // If any exception occurs, delete
                // already stored pictures.
                $this->deleteAll($filenames);

                throw $e;
            }
        }

        return $filenames;
    }

    protected function put(Storable $storable): string
    {
        $filename = $storable->getFilename();
        $path     = $this->getPath($filename);
        $result   = $this->storage->putStream(
            $path,
            $storable->getResource()
        );

        if ($result === true) {
            return $filename;
        }

        throw new RuntimeException(
            'Failed to store "' .
            $path .
            '"'
        );
    }

    /**
     * @param string[] $filenames
     */
    public function deleteAll(array $filenames): void
    {
        foreach ($filenames as $filename) {
            $this->delete($filename);
        }
    }

    public function delete(string $filename): void
    {
        $path   = $this->getPath($filename);
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

    public function fileExists(string $filename): bool
    {
        $path = $this->getPath($filename);

        return $this->storage->has($path);
    }
}
