<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class FileNotFoundExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (! $exception instanceof FileNotFoundException) {
            return;
        }

        $e = new NotFoundHttpException('Not found', $exception);
        $event->setThrowable($e);
    }
}
