<?php

declare(strict_types=1);

namespace App\Domain\Enum\Filter;

use MyCLabs\Enum\Enum;

/**
 * @method static ProductsSortBy NAME()
 * @method static ProductsSortBy PRICE()
 */
final class ProductsSortBy extends Enum
{
    private const NAME  = 'name';
    private const PRICE = 'price';
}
