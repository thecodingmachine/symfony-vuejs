<?php

declare(strict_types=1);

namespace App\Domain\Throwable;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use TheCodingMachine\Graphqlite\Validator\ValidationFailedException;

final class InvalidModel extends ValidationFailedException implements BusinessRule
{
    /**
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $constraintViolationList
     *
     * @throws InvalidModel
     */
    public static function throwException(ConstraintViolationListInterface $constraintViolationList): void
    {
        if ($constraintViolationList->count() > 0) {
            throw new self($constraintViolationList);
        }
    }
}
