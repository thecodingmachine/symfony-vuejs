<?php

declare(strict_types=1);

namespace App\Domain\Enum;

interface StringEnum
{
    /**
     * @return string[]
     */
    public static function values(): array;
}
