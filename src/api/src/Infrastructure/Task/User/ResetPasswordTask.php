<?php

declare(strict_types=1);

namespace App\Infrastructure\Task\User;

use App\Infrastructure\Task\AsyncTask;

final class ResetPasswordTask implements AsyncTask
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail() : string
    {
        return $this->email;
    }
}
