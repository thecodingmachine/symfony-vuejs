<?php

declare(strict_types=1);

namespace App\Infrastructure\Task;

use RuntimeException;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class SendEmailTaskHandler implements MessageHandlerInterface
{
    private Swift_Mailer $mailer;
    private Environment $twig;
    private TranslatorInterface $translator;
    private string $mailFrom;

    public function __construct(
        Swift_Mailer $mailer,
        Environment $twig,
        TranslatorInterface $translator,
        ContainerBagInterface $parameters
    ) {
        $this->mailer     = $mailer;
        $this->twig       = $twig;
        $this->translator = $translator;
        $this->mailFrom   = $parameters->get('app.mail_from');
    }

    public function __invoke(SendEmailTask $task): void
    {
        $translatedSubject = $this->translator
            ->trans(
                'subject',
                [],
                $task->getDomain(),
                $task->getLocale()
            );

        $context           = $task->getTemplateData();
        $context['domain'] = $task->getDomain();
        $context['locale'] = $task->getLocale();

        $message = (new Swift_Message($translatedSubject))
            ->setFrom($this->mailFrom)
            ->setTo($task->getTo())
            ->setBody(
                $this->twig->render(
                    $task->getTemplate(),
                    $context,
                ),
                'text/html'
            );

        $result = $this->mailer->send($message);
        if ($result === 0) {
            throw new RuntimeException(
                "Failed to send e-mail '" .
                $task->getDomain() .
                "' to " .
                $task->getTo()
            );
        }
    }
}
