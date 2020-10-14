<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Voter;

use App\Domain\Dao\OrderDao;
use App\Domain\Model\Order;
use App\Domain\Model\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

use function assert;
use function in_array;
use function is_string;

final class OrderVoter extends AppVoter
{
    public const DOWNLOAD_ORDER_INVOICE = 'DOWNLOAD_ORDER_INVOICE';

    private OrderDao $orderDao;

    public function __construct(Security $security, OrderDao $orderDao)
    {
        parent::__construct($security);
        $this->orderDao = $orderDao;
    }

    /**
     * @param mixed $subject
     */
    protected function supports(string $attribute, $subject): bool
    {
        if (
            ! in_array(
                $attribute,
                [
                    self::DOWNLOAD_ORDER_INVOICE,
                ]
            )
        ) {
            return false;
        }

        // We use is_string if we gives an id instead of an Order.
        // Mostly for classic Symfony routes.
        return $subject instanceof Order || is_string($subject);
    }

    /**
     * @param mixed $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // If the user is anonymous, do not grant access
        if (! $user instanceof UserInterface) {
            return false;
        }

        assert($user instanceof User);

        if ($subject instanceof Order) {
            $order = $subject;
        } else {
            $order = $this->orderDao->getById($subject);
        }

        switch ($attribute) {
            case self::DOWNLOAD_ORDER_INVOICE:
                // User should own the order.
                return $order->getUser()->getId() === $user->getId();

            default:
                return false;
        }
    }
}
