<?php

declare(strict_types=1);

namespace App\Domain\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class Unicity extends Constraint
{
    /** @var mixed $message */
    public $message;
    /** @var mixed $table */
    public $table;
    /** @var mixed $column */
    public $column;

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @return string[]
     */
    public function getRequiredOptions(): array
    {
        return [
            'message',
            'table',
            'column',
        ];
    }
}
