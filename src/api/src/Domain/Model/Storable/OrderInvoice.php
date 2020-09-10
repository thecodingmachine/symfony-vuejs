<?php

declare(strict_types=1);

namespace App\Domain\Model\Storable;

use Symfony\Component\Validator\Constraints as Assert;

use function strtolower;

final class OrderInvoice extends Storable
{
    /** @Assert\EqualTo("pdf") */
    public function getExtension(): string
    {
        return strtolower($this->fileInfo->getExtension());
    }
}
