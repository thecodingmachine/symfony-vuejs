<?php

declare(strict_types=1);

namespace App\UseCase\User\SignUp;

use App\Domain\Enum\Locale;
use App\Domain\Enum\Role;
use App\UseCase\AsyncTask;

final class SignUpTask implements AsyncTask
{
    private string $firstName;
    private string $lastName;
    private string $email;
    private Locale $locale;
    private Role $role;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        Locale $locale,
        Role $role
    ) {
        $this->firstName = $firstName;
        $this->lastName  = $lastName;
        $this->email     = $email;
        $this->locale    = $locale;
        $this->role      = $role;
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

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}
