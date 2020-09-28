<?php

declare(strict_types=1);

namespace App\UseCase\User\VerifyResetPasswordToken;

use App\Domain\Throwable\BusinessRule;
use GraphQL\Error\ClientAware;
use RuntimeException;

final class ResetPasswordTokenExpired extends RuntimeException implements ClientAware, BusinessRule
{
    public function __construct()
    {
        parent::__construct('', 400);
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
