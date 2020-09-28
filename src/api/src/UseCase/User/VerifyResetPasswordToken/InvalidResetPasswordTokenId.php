<?php

declare(strict_types=1);

namespace App\UseCase\User\VerifyResetPasswordToken;

use App\Domain\Throwable\BusinessRule;
use GraphQL\Error\ClientAware;
use RuntimeException;
use Throwable;

final class InvalidResetPasswordTokenId extends RuntimeException implements ClientAware, BusinessRule
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('', 400, $previous);
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'verifyResetPasswordToken';
    }
}
