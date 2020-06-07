<?php

declare(strict_types=1);

namespace App\Domain\Throwable\Invalid;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class InvalidPassword extends Invalid
{
    /**
     * @param ConstraintViolationListInterface<mixed> $constraintViolationList
     *
     * @throws InvalidPassword
     */
    public static function throwException(ConstraintViolationListInterface $constraintViolationList): void
    {
        if ($constraintViolationList->count() > 0) {
            throw new self($constraintViolationList);
        }
    }
}
