<?php

declare(strict_types=1);

namespace App\Application\User\UpdatePassword;

use App\Domain\Throwable\BaseThrowable;
use GraphQL\Error\ClientAware;
use RuntimeException;
use Throwable;

final class ResetPasswordTokenExpired extends RuntimeException implements ClientAware, BaseThrowable
{
    public function __construct(string $message = '', int $code = 400, ?Throwable $previous = null)
    {
        $code = $code !== 400 ? 400 : $code;
        parent::__construct($message, $code, $previous);
    }

    public function isClientSafe() : bool
    {
        return false;
    }

    public function getCategory() : string
    {
        return 'Reset password token expired';
    }
}
