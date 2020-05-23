<?php

declare(strict_types=1);

namespace App\Application\User\CreateUser;

use App\Domain\Throwable\InvalidModel;
use Symfony\Component\Validator\ConstraintViolationListInterface;

final class InvalidUser extends InvalidModel
{
    /**
     * @param ConstraintViolationListInterface<mixed> $constraintViolationList
     *
     * @throws InvalidUser
     */
    public static function throwException(ConstraintViolationListInterface $constraintViolationList) : void
    {
        parent::throwException($constraintViolationList);
    }
}
