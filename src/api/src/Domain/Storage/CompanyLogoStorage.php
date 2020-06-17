<?php

declare(strict_types=1);

namespace App\Domain\Storage;

use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;

final class CompanyLogoStorage extends PublicStorage
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

        return parent::put($logo);
    }
}
