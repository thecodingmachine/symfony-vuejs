<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Role ADMINISTRATOR()
 * @method static Role MERCHANT()
 * @method static Role CLIENT()
 */
final class Role extends Enum
{
    private const ADMINISTRATOR = 'ADMINISTRATOR';
    private const MERCHANT      = 'MERCHANT';
    private const CLIENT        = 'CLIENT';

    /**
     * @return string[]
     */
    public static function valuesAsArray(): array
    {
        return [
            self::ADMINISTRATOR,
            self::MERCHANT,
            self::CLIENT,
        ];
    }

    public static function getSymfonyRole(Role $role): string
    {
        return 'ROLE_' . $role;
    }
}
