<?php

declare(strict_types=1);

namespace App\UseCase\Company\DeleteCompanyLogo;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DeleteCompanyLogoTaskHandler implements MessageHandlerInterface
{
    private DeleteCompanyLogo $deleteCompanyLogo;

    public function __construct(DeleteCompanyLogo $deleteCompanyLogo)
    {
        $this->deleteCompanyLogo = $deleteCompanyLogo;
    }

    public function __invoke(DeleteCompanyLogoTask $task): void
    {
        $this->deleteCompanyLogo->deleteCompanyLogo(
            $task->getLogo()
        );
    }
}
