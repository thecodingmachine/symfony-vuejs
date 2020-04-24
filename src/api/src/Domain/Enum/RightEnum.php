<?php

declare(strict_types=1);

namespace App\Domain\Enum;

final class RightEnum implements StringEnum
{
    public const LIST_USERS  = 'LIST_USERS';
    public const CREATE_USER = 'CREATE_USER';
    public const EDIT_USER   = 'EDIT_USER';

    public const LIST_COMPANIES = 'LIST_COMPANIES';
    public const CREATE_COMPANY = 'CREATE_COMPANY';
    public const EDIT_COMPANY   = 'EDIT_COMPANY';

    public const LIST_PRODUCTS  = 'LIST_PRODUCTS';
    public const CREATE_PRODUCT = 'CREATE_PRODUCT';
    public const EDIT_PRODUCT   = 'EDIT_PRODUCT';

    /**
     * @return string[]
     */
    public static function values() : array
    {
        return [];
    }
}
