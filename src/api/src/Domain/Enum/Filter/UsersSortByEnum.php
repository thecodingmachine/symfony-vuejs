<?php

declare(strict_types=1);

namespace App\Domain\Enum\Filter;

use App\Domain\Enum\StringEnum;

final class UsersSortByEnum implements StringEnum
{
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME  = 'last_name';
    public const EMAIL      = 'email';
    public const ROLE       = 'role';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::FIRST_NAME,
            self::LAST_NAME,
            self::EMAIL,
            self::ROLE,
        ];
    }
}
