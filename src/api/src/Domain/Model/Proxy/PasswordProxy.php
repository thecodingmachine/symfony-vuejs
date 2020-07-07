<?php

declare(strict_types=1);

namespace App\Domain\Model\Proxy;

use Symfony\Component\Validator\Constraints as Assert;

final class PasswordProxy
{
    /**
     * @Assert\NotBlank(message="not_blank")
     * @Assert\Length(min=8, minMessage="min_length_8")
     * @Assert\NotCompromisedPassword(message="user.not_compromised_password")
     */
    private string $plainPassword;

    /** @Assert\Expression("this.getPlainPassword() === this.getPasswordConfirmation()", message="user.wrong_password_confirmation") */
    private string $passwordConfirmation;

    public function __construct(
        string $plainPassword,
        string $passwordConfirmation
    ) {
        $this->plainPassword        = $plainPassword;
        $this->passwordConfirmation = $passwordConfirmation;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function getPasswordConfirmation(): string
    {
        return $this->passwordConfirmation;
    }
}
