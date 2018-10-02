<?php

namespace App\Controller;

use Safe\Exceptions\JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Safe\json_encode;

final class IndexController extends Controller
{
    /**
     * @Route("/{vueRouting}", requirements={"vueRouting"="^(?!api|_(profiler|wdt)).+"}, name="index")
     * @param Request $request
     * @param null|string $vueRouting
     * @return Response
     * @throws JsonException
     */
    public function indexAction(Request $request, ?string $vueRouting = null): Response
    {
        $queryParameters = $request->query->all();
        return $this->render('base.html.twig', [
            'vueRouting' => \is_null($vueRouting) ? '/' : '/' . $vueRouting,
            'queryParameters' => json_encode($queryParameters),
        ]);
    }
}
