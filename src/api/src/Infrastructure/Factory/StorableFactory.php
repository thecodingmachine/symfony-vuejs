<?php

declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Model\Storable\CompanyLogo;
use Psr\Http\Message\UploadedFileInterface;

final class StorableFactory
{
    public static function createCompanyLogoFromUploadedFileInterface(
        ?UploadedFileInterface $uploadedFile = null
    ) : ?CompanyLogo {
        if ($uploadedFile === null) {
            return null;
        }

        $fileName = $uploadedFile->getClientFilename();
        $resource = $uploadedFile->getStream()->detach();

        return new CompanyLogo($fileName, $resource);
    }
}
