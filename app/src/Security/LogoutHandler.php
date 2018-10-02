<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

final class LogoutHandler implements LogoutHandlerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     * @return void
     */
    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        $response->headers->clearCookie('authenticated');
    }
}
