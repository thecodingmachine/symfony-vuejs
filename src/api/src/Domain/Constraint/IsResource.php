<?php

declare(strict_types=1);

namespace App\Domain\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class IsResource extends Constraint
{
    public string $message = 'Not a resource';
}
