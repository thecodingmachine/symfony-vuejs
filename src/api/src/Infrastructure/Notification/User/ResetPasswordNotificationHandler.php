<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\User;

use App\Application\User\ResetPassword\ResetPasswordNotification;
use App\Infrastructure\Notification\NotificationHandler;
use App\Infrastructure\Task\SendEmailTask;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use function Safe\sprintf;

final class ResetPasswordNotificationHandler extends NotificationHandler
{
    private string $webappUrl;
    private string $webappUpdatePasswordRouteFormat;

    public function __construct(MessageBusInterface $messageBus, ContainerBagInterface $parameters)
    {
        $this->webappUrl                       = $parameters->get('app.webapp_url');
        $this->webappUpdatePasswordRouteFormat = $parameters->get('app.webapp_update_password_route_format');
        parent::__construct($messageBus);
    }

    public function __invoke(ResetPasswordNotification $notification) : void
    {
        $domain = $notification->isNewUser() ?
            'emails_user_welcome_new_user' :
            'emails_user_reset_password';

        $template = $notification->isNewUser() ?
            'emails/user/welcome_new_user.html.twig' :
            'emails/user/reset_password.html.twig';

        $templateData = [
            'firstName' => $notification->getFirstName(),
            'lastName' => $notification->getLastName(),
            'update_password_url' =>
                $this->webappUrl .
                sprintf(
                    $this->webappUpdatePasswordRouteFormat,
                    $notification->getResetPasswordTokenId(),
                    $notification->getPlainToken()
                ),
        ];

        $task = new SendEmailTask(
            $domain,
            $notification->getLocale(),
            $notification->getEmail(),
            $template,
            $templateData
        );

        $this->messageBus->dispatch($task);
    }
}
