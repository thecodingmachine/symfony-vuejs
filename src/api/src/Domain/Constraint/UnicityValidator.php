<?php

declare(strict_types=1);

namespace App\Domain\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use TheCodingMachine\TDBM\TDBMService;

use function get_debug_type;

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
            throw new ConstraintDefinitionException(get_debug_type($constraint) . ' message argument is empty');
        }

        if (empty($constraint->table)) {
            throw new ConstraintDefinitionException(get_debug_type($constraint) . ' table argument is empty');
        }

        if (empty($constraint->column)) {
            throw new ConstraintDefinitionException(get_debug_type($constraint) . ' column argument is empty');
        }

        $getterValue = 'get' . $constraint->column;
        $getterId    = 'getid';

        $existingObject = $this->tdbmService->findObject(
            $constraint->table,
            [$constraint->column . ' = :value'],
            [
                'value' => $object->$getterValue(),
            ]
        );

        if ($existingObject === null) {
            return;
        }

        if ($existingObject->$getterId() === $object->$getterId()) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->atPath($constraint->column)
            ->addViolation();
    }
}
