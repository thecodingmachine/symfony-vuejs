<?php

declare(strict_types=1);

namespace App\Infrastructure\Task\User;

use App\Infrastructure\Task\AsyncTask;

final class SignUpClientTask implements AsyncTask
{
    private string $firstName;
    private string $lastName;
    private string $email;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email
    ) {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
        $this->email     = $email;
    }

    public function getFirstName() : string
    {
        return $this->firstName;
    }

    public function getLastName() : string
    {
        return $this->lastName;
    }

    public function getEmail() : string
    {
        return $this->email;
    }
}
