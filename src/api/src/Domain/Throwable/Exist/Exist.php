<?php

declare(strict_types=1);

namespace App\Domain\Throwable\Exist;

use App\Domain\Throwable\BaseThrowable;
use GraphQL\Error\ClientAware;
use RuntimeException;

abstract class Exist extends RuntimeException implements ClientAware, BaseThrowable
{
    public function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    public function isClientSafe() : bool
    {
        return true;
    }

    public function getCategory() : string
    {
        return 'Exist';
    }
}
