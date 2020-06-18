<?php

declare(strict_types=1);

namespace App\Domain\Enum\Filter;

use App\Domain\Enum\StringEnum;

final class ProductsSortByEnum implements StringEnum
{
    public const NAME  = 'name';
    public const PRICE = 'price';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::NAME,
            self::PRICE,
        ];
    }
}
