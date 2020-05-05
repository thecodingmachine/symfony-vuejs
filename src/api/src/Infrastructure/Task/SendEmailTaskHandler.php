<?php

declare(strict_types=1);

namespace App\Infrastructure\Task;

use App\Infrastructure\Helper\MiscConfiguration;
use RuntimeException;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;

final class SendEmailTaskHandler implements MessageHandlerInterface
{
    private Swift_Mailer $mailer;
    private Environment $twig;
    private string $mailFrom;

    public function __construct(Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer   = $mailer;
        $this->twig     = $twig;
        $this->mailFrom = MiscConfiguration::mustGetMailFrom();
    }

    public function __invoke(SendEmailTask $task) : void
    {
        $message = (new Swift_Message($task->getSubject()))
            ->setFrom($this->mailFrom)
            ->setTo($task->getTo())
            ->setBody(
                $this->twig->render(
                    $task->getTemplate(),
                    $task->getTemplateData(),
                ),
                'text/html'
            );

        $result = $this->mailer->send($message);
        if ($result === 0) {
            throw new RuntimeException(
                "Failed to send e-mail '" .
                $task->getSubject() .
                "' to " .
                $task->getTo()
            );
        }
    }
}
