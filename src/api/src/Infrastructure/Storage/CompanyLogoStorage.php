<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Store\CompanyLogoStore;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;

final class CompanyLogoStorage extends PublicStorage implements CompanyLogoStore
{
    /**
     * @throws InvalidCompanyLogo
     */
    public function write(CompanyLogo $logo): string
    {
        $violations = $this->validator->validate($logo);
        InvalidCompanyLogo::throwException($violations);

        parent::put(
            'company_logo/' . $logo->getGeneratedFileName(),
            $logo->getResource()
        );

        return $logo->getGeneratedFileName();
    }

    public function delete(string $fileName): void
    {
        parent::remove('company_logo/' . $fileName);
    }

    public function fileExists(string $fileName): bool
    {
        return parent::has('company_logo/' . $fileName);
    }
}
