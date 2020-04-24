<?php

declare(strict_types=1);

namespace App\Domain\Assert;

use Assert\Assertion as BaseAssertion;

final class Assertion extends BaseAssertion
{
    protected static $exceptionClass = 'App\Domain\Throwable\AssertionFailed';
}
