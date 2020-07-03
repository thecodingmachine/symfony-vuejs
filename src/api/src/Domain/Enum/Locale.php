<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Locale EN()
 * @method static Locale FR()
 */
final class Locale extends Enum
{
    private const EN = 'en';
    private const FR = 'fr';

    /**
     * @return string[]
     */
    public static function valuesAsArray(): array
    {
        return [
            self::EN,
            self::FR,
        ];
    }
}
