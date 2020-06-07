<?php

declare(strict_types=1);

namespace App\Domain\Throwable\NotFound;

use App\Domain\Throwable\BusinessRule;
use GraphQL\Error\ClientAware;
use RuntimeException;

abstract class NotFound extends RuntimeException implements ClientAware, BusinessRule
{
    public function __construct(string $message)
    {
        parent::__construct($message, 404);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'Not found';
    }
}
