<?php

declare(strict_types=1);

namespace App\Application\User\UpdatePassword;

use Symfony\Component\Validator\Constraints as Assert;

final class PasswordProxy
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(min = 8)
     * @Assert\NotCompromisedPassword
     */
    private string $plainPassword;

    public function __construct(string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPlainPassword() : string
    {
        return $this->plainPassword;
    }
}
