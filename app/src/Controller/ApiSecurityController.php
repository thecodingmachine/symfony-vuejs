<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class ApiSecurityController extends Controller
{
    /**
     * @Route("/api/security/login", name="login")
     * @return JsonResponse
     */
    public function loginAction(): JsonResponse
    {
        $securityCookie = new Cookie('authenticated', true, \time() + \intval(\ini_get('session.gc_maxlifetime')), '/', null, false, false);

        $response = new JsonResponse('authenticated!');
        $response->headers->setCookie($securityCookie);

        return $response;
    }

    /**
     * @Route("/api/security/logout", name="logout")
     * @throws \Exception
     */
    public function logoutAction()
    {
        throw new \Exception('This should not be reached!');
    }
}