<?php

declare(strict_types=1);

namespace App\Domain\Storage;

use App\Domain\Model\Storable\Storable;
use App\Domain\Throwable\InvalidStorable;
use League\Flysystem\FilesystemInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

abstract class Storage
{
    protected ParameterBagInterface $parameters;
    protected ValidatorInterface $validator;
    protected FilesystemInterface $storage;

    public function __construct(
        ParameterBagInterface $parameters,
        ValidatorInterface $validator,
        FilesystemInterface $storage
    ) {
        $this->parameters = $parameters;
        $this->validator  = $validator;
        $this->storage    = $storage;
    }

    abstract protected function getDirectoryName(): string;

    private function getPath(string $filename): string
    {
        return $this->getDirectoryName() . '/' . $filename;
    }

    /**
     * @param Storable[] $storables
     *
     * @throws InvalidStorable
     */
    protected function validateAll(array $storables): void
    {
        foreach ($storables as $storable) {
            $this->validate($storable);
        }
    }

    /**
     * @throws InvalidStorable
     */
    protected function validate(Storable $storable): void
    {
        $violations = $this->validator->validate($storable);
        InvalidStorable::throwExceptionWithKey($this->getDirectoryName(), $violations);
    }

    /**
     * @param Storable[] $storables
     *
     * @return string[]
     *
     * @throws InvalidStorable
     */
    public function writeAll(array $storables): array
    {
        $this->validateAll($storables);

        $filenames = [];
        foreach ($storables as $storable) {
            try {
                $filenames[] = $this->write($storable);
            } catch (InvalidStorable $e) {
                // pepakriz/phpstan-exception-rules limitation: "Catch statement does not know about runtime subtypes".
                // See https://github.com/pepakriz/phpstan-exception-rules#catch-statement-does-not-know-about-runtime-subtypes.
                $this->deleteAll($filenames);

                throw $e;
            } catch (Throwable $e) {
                // If any exception occurs, delete
                // already stored pictures.
                $this->deleteAll($filenames);

                throw $e;
            }
        }

        return $filenames;
    }

    /**
     * @throws InvalidStorable
     */
    public function write(Storable $storable): string
    {
        $this->validate($storable);

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

    public function getFileContent(string $filename): string
    {
        $path   = $this->getPath($filename);
        $result = $this->storage->read($path);

        if ($result !== false) {
            return $result;
        }

        throw new RuntimeException(
            'Failed to read "' .
            $path .
            '"'
        );
    }
}
