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
    private string $newPassword;

    /** @Assert\Expression("this.getNewPassword() === this.getPasswordConfirmation()", message="user.wrong_password_confirmation") */
    private string $passwordConfirmation;

    public function __construct(
        string $newPassword,
        string $passwordConfirmation
    ) {
        $this->newPassword          = $newPassword;
        $this->passwordConfirmation = $passwordConfirmation;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function getPasswordConfirmation(): string
    {
        return $this->passwordConfirmation;
    }
}
