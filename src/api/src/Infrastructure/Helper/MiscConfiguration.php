<?php

declare(strict_types=1);

namespace App\Infrastructure\Helper;

use RuntimeException;
use function getenv;
use function is_string;

final class MiscConfiguration
{
    private const MAIL_FROM                           = 'MAIL_FROM';
    private const WEBAPP_URL                          = 'WEBAPP_URL';
    private const WEBAPP_UPDATE_PASSWORD_ROUTE_FORMAT = 'WEBAPP_UPATE_PASSWORD_ROUTE_FORMAT';

    private static function mustGetStringFromEnvVar(string $envVar) : string
    {
        $value = getenv($envVar);
        if (is_string($value)) {
            return $value;
        }

        throw new RuntimeException(
            $envVar .
            ' have to be a string, got ' .
            $value
        );
    }

    public static function mustGetMailFrom() : string
    {
        return self::mustGetStringFromEnvVar(self::MAIL_FROM);
    }

    public static function mustGetWebAppUrl() : string
    {
        return self::mustGetStringFromEnvVar(self::WEBAPP_URL);
    }

    public static function mustGetWebAppUpdatePasswordRouteFormat() : string
    {
        return self::mustGetStringFromEnvVar(self::WEBAPP_UPDATE_PASSWORD_ROUTE_FORMAT);
    }
}
