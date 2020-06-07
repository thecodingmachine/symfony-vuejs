<?php

declare(strict_types=1);

namespace App\Domain\Throwable\Exists;

use App\Domain\Throwable\BusinessRule;
use GraphQL\Error\ClientAware;
use RuntimeException;

abstract class Exists extends RuntimeException implements ClientAware, BusinessRule
{
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'Exists';
    }
}
