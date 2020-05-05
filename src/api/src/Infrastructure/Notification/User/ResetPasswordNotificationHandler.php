<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification\User;

use App\Application\User\ResetPassword\ResetPasswordNotification;
use App\Infrastructure\Helper\MiscConfiguration;
use App\Infrastructure\Notification\Notifier;
use App\Infrastructure\Task\SendEmailTask;
use function Safe\sprintf;

final class ResetPasswordNotificationHandler extends Notifier
{
    public function __invoke(ResetPasswordNotification $notification) : void
    {
        $subject  = 'Reset password';
        $template = 'emails/user/reset_password.html.twig';
        if ($notification->isNewUser()) {
            $subject  = 'Welcome!';
            $template = 'emails/user/welcome_new_user.html.twig';
        }

        $task = new SendEmailTask(
            $notification->getEmail(),
            $subject,
            $template,
            [
                'firstName' => $notification->getFirstName(),
                'lastName' => $notification->getLastName(),
                'update_password_url' =>
                    MiscConfiguration::mustGetWebAppUrl() .
                    sprintf(
                        MiscConfiguration::mustGetWebAppUpdatePasswordRouteFormat(),
                        $notification->getResetPasswordTokenId(),
                        $notification->getPlainToken()
                    ),
            ]
        );

        $this->messageBus->dispatch($task);
    }
}
