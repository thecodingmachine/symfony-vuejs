<?php

declare(strict_types=1);

namespace App\Domain\Enum\Filter;

use MyCLabs\Enum\Enum;

/**
 * @method static CompaniesSortBy NAME()
 * @method static CompaniesSortBy WEBSITE()
 */
final class CompaniesSortBy extends Enum
{
    private const NAME    = 'name';
    private const WEBSITE = 'website';
}
