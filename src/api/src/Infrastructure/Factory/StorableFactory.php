<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Model\Storable\Storable;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use SplFileInfo;

use function Safe\fopen;

final class StorableFactory
{
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

    public static function createFromPath(
        string $path,
        string $resultClass
    ): Storable {
        $file     = new SplFileInfo($path);
        $resource = fopen($path, 'r');

        return new $resultClass($file->getFilename(), $resource);
    }
}
