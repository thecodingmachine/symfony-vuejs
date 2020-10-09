<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Order;

use App\Domain\Dao\OrderDao;
use App\Domain\Storage\OrderInvoiceStorage;
use App\Infrastructure\Controller\DownloadController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OrderInvoiceController extends DownloadController
{
    private OrderDao $orderDao;
    private OrderInvoiceStorage $orderInvoiceStorage;

    public function __construct(OrderDao $orderDao, OrderInvoiceStorage $orderInvoiceStorage)
    {
        $this->orderDao            = $orderDao;
        $this->orderInvoiceStorage = $orderInvoiceStorage;
    }

    /**
     * @Route("/orders/{id}/invoice", methods={"GET"})
     */
    public function downloadInvoice(string $id): Response
    {
        // TODO use a voter to check if authenticated user owns the invoice.

        // EventListener\NoBeanFoundExceptionListener will handle
        // the NoBeanFoundException (if any).
        $order    = $this->orderDao->getById($id);
        $filename = $order->getInvoice();
        // EventListener\FileNotFoundExceptionListener will handle
        // the FileNotFoundException (if any).
        $fileContent = $this->orderInvoiceStorage->getFileContent($filename);

        return $this->createResponseWithAttachment(
            'invoice_' . $id . '.pdf',
            $fileContent
        );
    }
}
