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
    public function __construct(string $filename, $resource, bool $overrideFilename = true)
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
     * @return Storable[]
     */
    public static function createAllFromUploadedFiles(
        array $uploadedFiles,
        string $resultClass
    ): array {
        $storables = [];

        foreach ($uploadedFiles as $uploadedFile) {
            $storables[] = self::createFromUploadedFile(
                $uploadedFile,
                $resultClass
            );
        }

        return $storables;
    }

    public static function createFromUploadedFile(
        UploadedFileInterface $uploadedFile,
        string $resultClass
    ): Storable {
        $fileName = $uploadedFile->getClientFilename();
        $resource = $uploadedFile->getStream()->detach();

        if ($fileName === null) {
            throw new RuntimeException(
                'File name from uploaded file should not be null'
            );
        }

        if ($resource === null) {
            throw new RuntimeException(
                'Resource from uploaded file should not be null'
            );
        }

        return new $resultClass($fileName, $resource);
    }

    /**
     * @param string[] $paths
     *
     * @return Storable[]
     */
    public static function createAllFromPaths(
        array $paths,
        string $resultClass
    ): array {
        $storables = [];

        foreach ($paths as $path) {
            $storables[] = self::createFromPath(
                $path,
                $resultClass
            );
        }

        return $storables;
    }

    public static function createFromPath(
        string $path,
        string $resultClass
    ): Storable {
        $file     = new SplFileInfo($path);
        $resource = fopen($path, 'r');

        return new $resultClass($file->getFilename(), $resource);
    }
}
