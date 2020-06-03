<?php

declare(strict_types=1);

namespace App\Domain\Model\Storable;

use Symfony\Component\Validator\Constraints as Assert;
use function strtolower;

final class CompanyLogo extends Storable
{
    /** @Assert\Choice({"png", "jpg"}) */
    public function getExtension() : string
    {
        return strtolower($this->fileInfo->getExtension());
    }
}
