<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class Notifier implements MessageHandlerInterface
{
    protected MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }
}
