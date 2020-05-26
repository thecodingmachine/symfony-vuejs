<?php

declare(strict_types=1);

namespace App\Domain\Repository\Search;

use App\Domain\Enum\StringEnum;

final class SortOrderEnum implements StringEnum
{
    public const ASC  = 'ASC';
    public const DESC = 'DESC';

    /**
     * @return string[]
     */
    public static function values() : array
    {
        return [
            self::ASC,
            self::DESC,
        ];
    }
}
