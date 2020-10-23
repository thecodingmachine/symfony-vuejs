<?php

declare(strict_types=1);

namespace App\Domain\Throwable;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use TheCodingMachine\Graphqlite\Validator\ValidationFailedException;

use function assert;

final class InvalidStorable extends ValidationFailedException implements BusinessRule
{
    /**
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $constraintViolationList
     */
    public function __construct(string $key, ConstraintViolationListInterface $constraintViolationList)
    {
        $customizedConstraintViolationList = new ConstraintViolationList();
        foreach ($constraintViolationList as $constraint) {
            assert($constraint instanceof ConstraintViolationInterface);
            $customizedConstraint = new ConstraintViolation(
                $constraint->getMessage(),
                $constraint->getMessageTemplate(),
                $constraint->getParameters(),
                $constraint->getRoot(),
                $key,
                $constraint->getInvalidValue(),
                $constraint->getPlural(),
                $constraint->getCode()
            );

            $customizedConstraintViolationList->add($customizedConstraint);
        }

        parent::__construct($customizedConstraintViolationList);
    }

    /**
     * @param ConstraintViolationListInterface<ConstraintViolationInterface> $constraintViolationList
     *
     * @throws InvalidStorable
     */
    public static function throwExceptionWithKey(string $key, ConstraintViolationListInterface $constraintViolationList): void
    {
        if ($constraintViolationList->count() > 0) {
            throw new self($key, $constraintViolationList);
        }
    }
}
