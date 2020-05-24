<?php

declare(strict_types=1);

namespace App\Application\User\UpdatePassword;

use App\Domain\Throwable\InvalidModel;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class InvalidPassword extends InvalidModel
{
    /**
     * @param ConstraintViolationListInterface<mixed> $constraintViolationList
     *
     * @throws InvalidPassword
     */
    public static function throwException(ConstraintViolationListInterface $constraintViolationList) : void
    {
        if ($constraintViolationList->count() > 0) {
            throw new self($constraintViolationList);
        }
    }
}
