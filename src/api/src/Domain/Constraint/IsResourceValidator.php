<?php

declare(strict_types=1);

namespace App\Domain\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function is_resource;

final class IsResourceValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (! $constraint instanceof IsResource) {
            throw new UnexpectedTypeException($constraint, IsResource::class);
        }

        if (is_resource($value) !== false) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->atPath('resource')
            ->addViolation();
    }
}
