<?php

declare(strict_types=1);

namespace App\Domain\Throwable\Invalid;

use App\Domain\Throwable\BusinessRule;
use TheCodingMachine\Graphqlite\Validator\ValidationFailedException;

abstract class Invalid extends ValidationFailedException implements BusinessRule
{
}
