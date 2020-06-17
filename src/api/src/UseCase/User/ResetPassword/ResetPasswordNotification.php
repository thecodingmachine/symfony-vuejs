<?php

declare(strict_types=1);

namespace App\UseCase\User\ResetPassword;

use App\Domain\Model\ResetPasswordToken;
use App\Domain\Model\User;
use App\UseCase\Notification;

class ResetPasswordNotification implements Notification
{
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $locale;
    private string $resetPasswordTokenId;
    private string $plainToken;
    private bool $isNewUser;

    public function __construct(
        User $user,
        ResetPasswordToken $resetPasswordToken,
        string $plainToken
    ) {
        $this->firstName            = $user->getFirstName();
        $this->lastName             = $user->getLastName();
        $this->email                = $user->getEmail();
        $this->locale               = $user->getLocale();
        $this->resetPasswordTokenId = $resetPasswordToken->getId();
        $this->plainToken           = $plainToken;
        $this->isNewUser            = $user->getPassword() === null;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getResetPasswordTokenId(): string
    {
        return $this->resetPasswordTokenId;
    }

    public function getPlainToken(): string
    {
        return $this->plainToken;
    }

    public function isNewUser(): bool
    {
        return $this->isNewUser;
    }
}
