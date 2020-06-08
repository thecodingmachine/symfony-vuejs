<?php

declare(strict_types=1);

namespace App\Domain\Model\Storable;

use App\Domain\Constraint as DomainAssert;
use Ramsey\Uuid\Uuid;
use SplFileInfo;

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
}
