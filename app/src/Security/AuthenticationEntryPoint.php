<?php declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * This is required because otherwise symfony would throw HTTP 500 response,
 * when anonymous user try to access user protected route.
 * For some reason in @see JsonLoginFactory::createListener
 * entiry point is not defined likein @see FormLoginFactory::createEntryPoint
 * and it defaults to null
 * When it default to null @see InsufficientAuthenticationException
 * is being created, and in case of null entry point thrown here @see ExceptionListener::startAuthentication
 *
 * if (null === $this->authenticationEntryPoint) {
 *     throw $authException; // instance of: @see InsufficientAuthenticationException
 * }
 *
 * there are many issue ticket for this, dating back to 2013 so maybe some day it would be fixed:
 * @link https://github.com/symfony/symfony/issues/8467
 * @link https://github.com/symfony/symfony/issues/25806
 * @link https://github.com/symfony/symfony/issues/20233
 * @link https://github.com/Rebolon/php-sf-flex-webpack-encore-vuejs/issues/31
 */
final class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse([], Response::HTTP_FORBIDDEN);
    }
}
