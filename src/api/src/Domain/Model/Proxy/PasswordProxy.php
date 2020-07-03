<?php

declare(strict_types=1);

namespace App\Domain\Model\Proxy;

use Symfony\Component\Validator\Constraints as Assert;

final class PasswordProxy
{
    /**
     * @Assert\NotBlank(message="assert.not_blank")
     * @Assert\Length(min=8, minMessage="assert.min_length_8")
     * @Assert\NotCompromisedPassword(message="assert.not_compromised_password")
     */
    private string $plainPassword;

    public function __construct(string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }
}
