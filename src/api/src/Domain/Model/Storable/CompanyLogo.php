<?php

declare(strict_types=1);

namespace App\Domain\Model\Storable;

use Symfony\Component\Validator\Constraints as Assert;

final class CompanyLogo extends Storable
{
    /** @Assert\Choice({"png", "jpg"}) */
    protected function getExtension() : string
    {
        return $this->fileInfo->getExtension();
    }
}
