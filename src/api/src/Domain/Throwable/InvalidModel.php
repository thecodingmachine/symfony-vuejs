<?php

declare(strict_types=1);

namespace App\Domain\Throwable;

use TheCodingMachine\Graphqlite\Validator\ValidationFailedException;

abstract class InvalidModel extends ValidationFailedException implements BusinessRule
{
}
