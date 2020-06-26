<?php

declare(strict_types=1);

namespace App\UseCase\Company\DeleteCompanyLogo;

use App\UseCase\AsyncTask;

final class DeleteCompanyLogoTask implements AsyncTask
{
    private string $logo;

    public function __construct(string $logo)
    {
        $this->logo = $logo;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }
}
