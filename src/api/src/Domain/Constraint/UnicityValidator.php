<?php

declare(strict_types=1);

namespace App\Domain\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\RuntimeException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use TheCodingMachine\TDBM\TDBMService;

final class UnicityValidator extends ConstraintValidator
{
    private TDBMService $tdbmService;

    public function __construct(TDBMService $tdbmService)
    {
        $this->tdbmService = $tdbmService;
    }

    /**
     * @param mixed $object
     */
    public function validate($object, Constraint $constraint): void
    {
        if (! $constraint instanceof Unicity) {
            throw new UnexpectedTypeException($constraint, Unicity::class);
        }

        if (empty($constraint->message)) {
            throw new RuntimeException(Unicity::class . ' message argument is empty');
        }

        if (empty($constraint->table)) {
            throw new RuntimeException(Unicity::class . ' table argument is empty');
        }

        if (empty($constraint->column)) {
            throw new RuntimeException(Unicity::class . ' column argument is empty');
        }

        $getter = 'get' . $constraint->column;

        $existingObject = $this->tdbmService->findObject(
            $constraint->table,
            [$constraint->column . ' = :value'],
            [
                'value' => $object->$getter(),
            ]
        );

        if ($existingObject !== $object) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->atPath($constraint->column)
            ->addViolation();
    }
}
