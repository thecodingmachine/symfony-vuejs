<?php

declare(strict_types=1);

namespace App\UseCase\Order;

use App\Domain\Dao\OrderDao;
use App\Domain\Model\Order;
use App\Domain\Model\Product;
use App\Domain\Model\Storable\OrderInvoice;
use App\Domain\Model\User;
use App\Domain\Storage\OrderInvoiceStorage;
use App\Domain\Throwable\InvalidModel;
use TheCodingMachine\GraphQLite\Annotations\InjectUser;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

use function dirname;

final class CreateOrder
{
    private OrderDao $orderDao;
    private OrderInvoiceStorage $orderInvoiceStorage;

    public function __construct(OrderDao $orderDao, OrderInvoiceStorage $orderInvoiceStorage)
    {
        $this->orderDao            = $orderDao;
        $this->orderInvoiceStorage = $orderInvoiceStorage;
    }

    /**
     * @throws InvalidModel
     *
     * @Mutation
     * @InjectUser(for="$user")
     */
    public function createOrder(
        User $user,
        Product $product,
        int $quantity
    ): Order {
        $order = new Order(
            $user,
            $product,
            $quantity,
            $product->getPrice()
        );

        // Validate the order before uploading
        // its invoice.
        $this->orderDao->validate($order);

        // You could call here your "Invoice generator service".
        $invoice = OrderInvoice::createFromPath(
            dirname(__FILE__) . '/FakeOrderInvoice.pdf',
        );

        // Upload the invoice.
        $filename = $this->orderInvoiceStorage->write($invoice);
        $order->setInvoice($filename);

        $this->orderDao->save($order);

        return $order;
    }
}
