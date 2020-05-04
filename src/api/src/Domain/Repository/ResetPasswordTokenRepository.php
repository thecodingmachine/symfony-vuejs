<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\ResetPasswordToken;
use App\Domain\Throwable\NotFound\ResetPasswordTokenNotFoundById;

interface ResetPasswordTokenRepository
{
    public function save(ResetPasswordToken $resetPasswordToken) : void;

    public function delete(ResetPasswordToken $resetPasswordToken) : void;

    /**
     * @throws ResetPasswordTokenNotFoundById
     */
    public function mustFindOneById(string $id) : ResetPasswordToken;
}
