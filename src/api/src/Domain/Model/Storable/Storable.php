<?php

declare(strict_types=1);

namespace App\Domain\Model\Storable;

use App\Domain\Constraint as DomainAssert;
use Psr\Http\Message\UploadedFileInterface;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use SplFileInfo;

use function Safe\fopen;

abstract class Storable
{
    protected SplFileInfo $fileInfo;
    private string $filename;
    /**
     * @var resource $resource
     * @DomainAssert\IsResource
     */
    private $resource;

    /**
     * @param resource $resource
     */
    final public function __construct(string $filename, $resource, bool $overrideFilename = true)
    {
        $this->fileInfo = new SplFileInfo($filename);
        $this->filename = $overrideFilename === true ?
            Uuid::uuid4()->toString() :
            $filename;

        $this->resource = $resource;
    }

    public function getFilename(): string
    {
        return $this->filename . '.' . $this->getExtension();
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    abstract public function getExtension(): string;

    /**
     * @param UploadedFileInterface[] $uploadedFiles
     *
     * @return static[]
     */
    public static function createAllFromUploadedFiles(array $uploadedFiles): array
    {
        $storables = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $storables[] = self::createFromUploadedFile($uploadedFile);
        }

        return $storables;
    }

    /**
     * @return static
     */
    public static function createFromUploadedFile(UploadedFileInterface $uploadedFile)
    {
        $fileName = $uploadedFile->getClientFilename();
        $resource = $uploadedFile->getStream()->detach();

        if ($fileName === null) {
            throw new RuntimeException(
                'Filename from uploaded file is null'
            );
        }

        if ($resource === null) {
            throw new RuntimeException(
                'Resource from uploaded file is null'
            );
        }

        return new static($fileName, $resource);
    }

    /**
     * @param string[] $paths
     *
     * @return static[]
     */
    public static function createAllFromPaths(array $paths): array
    {
        $storables = [];

        foreach ($paths as $path) {
            $storables[] = self::createFromPath($path);
        }

        return $storables;
    }

    /**
     * @return static
     */
    public static function createFromPath(string $path): Storable
    {
        $file     = new SplFileInfo($path);
        $resource = fopen($path, 'r');

        return new static($file->getFilename(), $resource);
    }
}
