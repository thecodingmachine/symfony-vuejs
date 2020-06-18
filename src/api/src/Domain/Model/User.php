<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Model\Generated\BaseUser;
use Serializable;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use TheCodingMachine\GraphQLite\Annotations\Type;

use function Safe\password_hash;
use function serialize;
use function unserialize;

use const PASSWORD_DEFAULT;

/**
 * The User class maps the 'users' table in database.
 *
 * @Type
 */
class User extends BaseUser implements UserInterface, Serializable, EquatableInterface
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(max = 255)
     */
    public function getFirstName(): string
    {
        return parent::getFirstName();
    }

    /**
     * @Assert\NotBlank
     * @Assert\Length(max = 255)
     */
    public function getLastName(): string
    {
        return parent::getLastName();
    }

    /**
     * @Assert\NotBlank
     * @Assert\Length(max = 255)
     * @Assert\Email
     */
    public function getEmail(): string
    {
        return parent::getEmail();
    }

    public function setPassword(?string $password): void
    {
        if ($password === null) {
            parent::setPassword($password);

            return;
        }

        parent::setPassword(password_hash($password, PASSWORD_DEFAULT));
    }

    /**
     * @Assert\Choice(callback={"App\Domain\Enum\LocaleEnum", "values"})
     */
    public function getLocale(): string
    {
        return parent::getLocale();
    }

    /**
     * @Assert\Choice(callback={"App\Domain\Enum\RoleEnum", "values"})
     */
    public function getRole(): string
    {
        return parent::getRole();
    }

    /*
     * This whole part with the $userNameFromSerialize property is a hack to make User serializable.
     * Actually, if we implement the EquatableInterface from Symfony, the only thing that needs to be serialized is
     * the userName (the email in our case).
     * Therefore, we put the user name in a property that can be serialized/unserialized via the methods of the
     * Serializable interface.
     * The unserialized object only contains the "$userNameFromSerialize" property but this is not a problem.
     * The UserProvider will be called to load the full object from the user name.
     */
    private ?string $userNameFromSerialize = null;

    public function getUsername(): string
    {
        if ($this->userNameFromSerialize === null) {
            $this->userNameFromSerialize = $this->getEmail();
        }

        return $this->userNameFromSerialize;
    }

    public function eraseCredentials(): void
    {
        // No need to do anything. No sensitive data is ever stored.
    }

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return [$this->getRole()];
    }

    public function serialize(): string
    {
        return serialize([$this->userNameFromSerialize]);
    }

    /**
     * phpcs:disable
     *
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        // phpcs:enable
        [$this->userNameFromSerialize] = unserialize($serialized);
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     */
    public function isEqualTo(UserInterface $user): bool
    {
        return $this->getUsername() === $user->getUsername();
    }
}
