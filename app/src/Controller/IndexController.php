<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends Controller
{
    /**
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).+"}, name="index")
     * @param Request $request
     * @param null|string $vueRouting
     * @return Response
     */
    public function indexAction(Request $request, ?string $vueRouting = null): Response
    {
        $queryParameters = $request->query->all();
        return $this->render('base.html.twig', [
            'vueRouting' => \is_null($vueRouting) ? '/' : '/' . $vueRouting,
            'queryParameters' => \json_encode($queryParameters),
        ]);
    }
}