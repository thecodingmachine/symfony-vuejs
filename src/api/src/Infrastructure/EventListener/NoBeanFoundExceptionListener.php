<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TheCodingMachine\TDBM\NoBeanFoundException;

final class NoBeanFoundExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (! $exception instanceof NoBeanFoundException) {
            return;
        }

        $e = new NotFoundHttpException('Not found', $exception);
        $event->setThrowable($e);
    }
}
