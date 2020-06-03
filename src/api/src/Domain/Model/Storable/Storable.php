<?php

declare(strict_types=1);

namespace App\Domain\Model\Storable;

use App\Domain\Constraint as DomainAssert;
use Ramsey\Uuid\Uuid;
use SplFileInfo;

abstract class Storable
{
    protected SplFileInfo $fileInfo;
    private string $generatedBaseFileName;
    /**
     * @var resource $resource
     * @DomainAssert\IsResource
     */
    private $resource;

    /**
     * @param resource $resource
     */
    public function __construct(string $fileName, $resource)
    {
        $this->fileInfo              = new SplFileInfo($fileName);
        $this->generatedBaseFileName = Uuid::uuid4()->toString();
        $this->resource              = $resource;
    }

    public function getOriginalFileName() : string
    {
        return $this->fileInfo->getBasename('.' . $this->fileInfo->getExtension()) . '.' . $this->getExtension();
    }

    public function getGeneratedFileName() : string
    {
        return $this->generatedBaseFileName . '.' . $this->getExtension();
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    abstract public function getExtension() : string;
}
