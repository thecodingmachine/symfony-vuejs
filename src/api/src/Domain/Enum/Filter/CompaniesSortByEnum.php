<?php

declare(strict_types=1);

namespace App\Domain\Enum\Filter;

use App\Domain\Enum\StringEnum;

final class CompaniesSortByEnum implements StringEnum
{
    public const NAME    = 'name';
    public const WEBSITE = 'website';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::NAME,
            self::WEBSITE,
        ];
    }
}
