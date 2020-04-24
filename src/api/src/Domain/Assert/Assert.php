<?php

declare(strict_types=1);

namespace App\Domain\Assert;

use Assert\Assert as BaseAssert;

final class Assert extends BaseAssert
{
    protected static $assertionClass = 'App\Domain\Assert\Assertion';
}
