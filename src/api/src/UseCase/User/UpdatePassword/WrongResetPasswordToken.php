<?php

declare(strict_types=1);

namespace App\UseCase\User\UpdatePassword;

use App\Domain\Throwable\BusinessRule;
use GraphQL\Error\ClientAware;
use RuntimeException;

final class WrongResetPasswordToken extends RuntimeException implements ClientAware, BusinessRule
{
    public function __construct()
    {
        parent::__construct('', 400);
    }

    public function isClientSafe(): bool
    {
        return false;
    }

    public function getCategory(): string
    {
        return 'Wrong reset password token';
    }
}
