<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class RoleEnum implements StringEnum
{
    public const ADMINISTRATOR = 'ADMINISTRATOR';
    public const COMPANY       = 'COMPANY';
    public const CLIENT        = 'CLIENT';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::ADMINISTRATOR,
            self::COMPANY,
            self::CLIENT,
        ];
    }
}
