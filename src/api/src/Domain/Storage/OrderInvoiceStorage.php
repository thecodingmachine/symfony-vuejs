<?php

declare(strict_types=1);

namespace App\Domain\Storage;

final class OrderInvoiceStorage extends PrivateStorage
{
    protected function getDirectoryName(): string
    {
        return 'order_invoice';
    }
}
