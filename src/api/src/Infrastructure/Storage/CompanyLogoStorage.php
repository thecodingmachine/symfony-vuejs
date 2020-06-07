<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Store\CompanyLogoStore;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;

final class CompanyLogoStorage extends PublicStorage implements CompanyLogoStore
{
    protected function getDirectoryName(): string
    {
        return 'company_logo';
    }

    /**
     * @throws InvalidCompanyLogo
     */
    public function write(CompanyLogo $logo): string
    {
        $violations = $this->validator->validate($logo);
        InvalidCompanyLogo::throwException($violations);

        parent::put(
            $logo->getGeneratedFileName(),
            $logo->getResource()
        );

        return $logo->getGeneratedFileName();
    }
}
