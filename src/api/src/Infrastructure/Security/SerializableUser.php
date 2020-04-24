<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\Model\Company;
use App\Domain\Model\Right;
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
    /** @var Company[] */
    private array $companies;
    /** @var Right[] */
    private array $rights;
    /** @var string[] */
    private array $symfonyRoles;

    public function __construct(User $user)
    {
        if ($user->getPassword() === null) {
            throw new RuntimeException('Password should not be null');
        }

        $this->id           = $user->getId();
        $this->firstName    = $user->getFirstName();
        $this->lastName     = $user->getLastName();
        $this->email        = $user->getEmail();
        $this->password     = $user->getPassword();
        $this->companies    = $user->getCompanies();
        $this->rights       = $user->getRole()->getRights();
        $this->symfonyRoles = [];

        foreach ($this->rights as $right) {
            // 'ROLE_' is a required prefix for Symfony.
            $this->symfonyRoles[] = 'ROLE_' . $right->getCode();
        }
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
     * @return Company[]
     *
     * @Field
     */
    public function getCompanies() : array
    {
        return $this->companies;
    }

    /**
     * @return Right[]
     *
     * @Field
     */
    public function getRights() : array
    {
        return $this->rights;
    }

    /**
     * @return string[]
     */
    public function getRoles() : array
    {
        return $this->symfonyRoles;
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
