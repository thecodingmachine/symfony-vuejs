<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class LocaleEnum implements StringEnum
{
    public const EN = 'en';
    public const FR = 'fr';

    /**
     * @return string[]
     */
    public static function values() : array
    {
        return [
            self::EN,
            self::FR,
        ];
    }
}
