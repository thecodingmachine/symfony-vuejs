<?php

declare(strict_types=1);

namespace App\Domain\Model\Storable;

use App\Domain\Constraint\IsResourceValidator as ResourceAssert;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Storable
{
    protected SplFileInfo $fileInfo;
    private string $generatedBaseFileName;
    /**
     * @var resource|null $resource
     * @Assert\NotNull
     * @ResourceAssert\IsResource
     */
    private $resource;

    /**
     * @param resource|null $resource
     */
    public function __construct(?string $fileName, $resource)
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
        if ($this->resource === null) {
            throw new RuntimeException(
                'Instance of ' . self::class . ' should have been validated'
            );
        }

        return $this->resource;
    }

    abstract protected function getExtension() : string;
}
