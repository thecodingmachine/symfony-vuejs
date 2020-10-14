<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

abstract class AppVoter extends Voter
{
    protected Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
}
