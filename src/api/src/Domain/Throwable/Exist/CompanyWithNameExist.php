<?php

declare(strict_types=1);

namespace App\Domain\Throwable\Exist;

final class CompanyWithNameExist extends Exist
{
    public function __construct(string $name)
    {
        parent::__construct('"' . $name . '" is already assigned to a company');
    }
}
