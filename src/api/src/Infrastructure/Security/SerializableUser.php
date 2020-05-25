<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Model\Company;
use App\Domain\Model\User;
use RuntimeException;
use Symfony\Component\Security\Core\User\UserInterface;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;

/**
 * @Type
 */
final class SerializableUser implements UserInterface
{
    private string $id;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $password;
    private string $locale;
    private string $role;
    /** @var Company[] */
    private array $companies;

    public function __construct(User $user)
    {
        if ($user->getPassword() === null) {
            throw new RuntimeException('Password should not be null');
        }

        $this->id        = $user->getId();
        $this->firstName = $user->getFirstName();
        $this->lastName  = $user->getLastName();
        $this->email     = $user->getEmail();
        $this->password  = $user->getPassword();
        $this->locale    = $user->getLocale();
        $this->role      = $user->getRole();
        $this->companies = $user->getCompanies();
    }

    /**
     * @Field
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @Field
     */
    public function getFirstName() : string
    {
        return $this->firstName;
    }

    /**
     * @Field
     */
    public function getLastName() : string
    {
        return $this->lastName;
    }

    /**
     * @Field
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @Field
     */
    public function getLocale() : string
    {
        return $this->locale;
    }

    /**
     * @Field
     */
    public function getRole() : string
    {
        return $this->role;
    }

    /**
     * @return Company[]
     *
     * @Field
     */
    public function getCompanies() : array
    {
        return $this->companies;
    }

    /**
     * @return string[]
     */
    public function getRoles() : array
    {
        return [
            // 'ROLE_' is a required prefix for Symfony.
            'ROLE_' . $this->role,
        ];
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function getSalt() : ?string
    {
        return null;
    }

    public function getUsername() : string
    {
        return $this->email;
    }

    public function eraseCredentials() : void
    {
    }
}
