<?php

declare(strict_types=1);

namespace App\Domain\Enum\Filter;

use MyCLabs\Enum\Enum;

/**
 * @method static OrdersSortBy PRODUCT_NAME()
 * @method static OrdersSortBy UNIT_PRICE()
 * @method static OrdersSortBy QUANTITY()
 * @method static OrdersSortBy TOTAL()
 */
final class OrdersSortBy extends Enum
{
    private const PRODUCT_NAME = 'products.name';
    private const UNIT_PRICE   = 'unit_price';
    private const QUANTITY     = 'quantity';
    private const TOTAL        = self::UNIT_PRICE . ' * ' . self::QUANTITY;
}
