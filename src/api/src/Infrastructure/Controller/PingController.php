<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PingController extends AbstractController
{
    /**
     * @Route("/ping", methods={"GET"})
     */
    public function ping(): Response
    {
        // A basic endpoint for your
        // health check probes.
        return new Response(
            'pong'
        );
    }
}
