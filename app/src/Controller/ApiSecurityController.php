<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

final class ApiSecurityController extends Controller
{
    /**
     * @Route("/api/security/login", name="login")
     * @return JsonResponse
     */
    public function loginAction(): JsonResponse
    {
        return new JsonResponse('authenticated!');
    }

    /**
     * @Route("/api/security/logout", name="logout")
     * @throws \Exception
     */
    public function logoutAction()
    {
        throw new \Exception('This should not be reached!');
    }

    /**
     * @Rest\Get("/api/security/is-authenticated", name="isAuthenticated")
     * @return JsonResponse
     */
    public function isAuthenticatedAction(): JsonResponse
    {
        return $this->isGranted('IS_AUTHENTICATED_FULLY') ? new JsonResponse('authenticated!') : new JsonResponse('not authenticated!', 401);
    }
}