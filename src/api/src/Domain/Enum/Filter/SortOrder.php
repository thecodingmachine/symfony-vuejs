<?php

declare(strict_types=1);

namespace App\Domain\Enum\Filter;

use MyCLabs\Enum\Enum;

/**
 * @method static SortOrder ASC()
 * @method static SortOrder DESC()
 */
final class SortOrder extends Enum
{
    private const ASC  = 'ASC';
    private const DESC = 'DESC';
}
