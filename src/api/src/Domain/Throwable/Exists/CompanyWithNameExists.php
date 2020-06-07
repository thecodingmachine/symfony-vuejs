<?php

declare(strict_types=1);

namespace App\Domain\Throwable\Exists;

final class CompanyWithNameExists extends Exists
{
    public function __construct(string $name)
    {
        parent::__construct('"' . $name . '" is already assigned to a company');
    }
}
