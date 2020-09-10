<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Domain\Dao\OrderDao;
use App\Domain\Storage\OrderInvoiceStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

final class OrderInvoiceController extends AbstractController
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
        // TODO listener for catching NoBeanFoundException?
        $order    = $this->orderDao->getById($id);
        $filename = $order->getInvoice();

        if (! $this->orderInvoiceStorage->fileExists($filename)) {
            throw $this->createNotFoundException();
        }

        $fileContent = $this->orderInvoiceStorage->getFileContent($filename);

        $response          = new Response($fileContent);
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'invoice_' . $id . '.pdf'
        );
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
