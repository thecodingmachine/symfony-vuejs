<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use function strlen;

final class LocaleSubscriber implements EventSubscriberInterface
{
    private string $defaultLocale;

    public function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale  = $request->headers->get('Accept-Language');
        $locale  =  $locale === null || strlen($locale) !== 2 ? $this->defaultLocale : $locale;
        $request->setLocale($locale);
    }

    /**
     * @return array<string,mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // Must be registered before (i.e. with a higher priority than) the default Locale listener onKernelRequest.
            // Run php bin/console debug:event kernel.request to see the order.
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
