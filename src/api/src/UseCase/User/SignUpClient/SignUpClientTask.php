<?php

declare(strict_types=1);

namespace App\UseCase\User\SignUpClient;

use App\UseCase\AsyncTask;

final class SignUpClientTask implements AsyncTask
{
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $locale;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $locale
    ) {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
        $this->email     = $email;
        $this->locale    = $locale;
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
}
