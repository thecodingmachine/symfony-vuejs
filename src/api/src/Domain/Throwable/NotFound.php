<?php

declare(strict_types=1);

namespace App\Domain\Throwable;

use GraphQL\Error\ClientAware;
use RuntimeException;

final class NotFound extends RuntimeException implements ClientAware, BusinessRule
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
