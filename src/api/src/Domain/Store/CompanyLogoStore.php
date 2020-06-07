<?php

declare(strict_types=1);

namespace App\Domain\Store;

use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;

interface CompanyLogoStore
{
    /**
     * @throws InvalidCompanyLogo
     */
    public function write(CompanyLogo $logo): string;

    public function delete(string $fileName): void;

    public function fileExists(string $fileName): bool;
}
