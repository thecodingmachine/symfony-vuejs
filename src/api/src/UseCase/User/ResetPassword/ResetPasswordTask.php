<?php

declare(strict_types=1);

namespace App\UseCase\User\ResetPassword;

use App\UseCase\AsyncTask;

final class ResetPasswordTask implements AsyncTask
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
