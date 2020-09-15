<?php

declare(strict_types=1);

namespace App\UseCase\Order;

use App\Domain\Dao\OrderDao;
use App\Domain\Enum\Filter\OrdersSortBy;
use App\Domain\Enum\Filter\SortOrder;
use App\Domain\Model\Order;
use App\Domain\Model\User;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\TDBM\ResultIterator;

final class MyOrders
{
    private OrderDao $orderDao;

    public function __construct(OrderDao $orderDao)
    {
        $this->orderDao = $orderDao;
    }

    /**
     * @return Order[]|ResultIterator
     *
     * @Query
     * @InjectUser(for="$user")
     */
    public function myOrders(
        User $user,
        ?string $search = null,
        ?OrdersSortBy $sortBy = null,
        ?SortOrder $sortOrder = null
    ): ResultIterator {
        return $this->orderDao->search(
            $search,
            $user,
            $sortBy,
            $sortOrder
        );
    }
}
