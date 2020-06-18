<?php

declare(strict_types=1);

namespace App\Domain\Throwable\Invalid;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class InvalidProductsFilters extends Invalid
{
    /**
     * @param ConstraintViolationListInterface<mixed> $constraintViolationList
     *
     * @throws InvalidProductsFilters
     */
    public static function throwException(ConstraintViolationListInterface $constraintViolationList): void
    {
        if ($constraintViolationList->count() > 0) {
            throw new self($constraintViolationList);
        }
    }
}
