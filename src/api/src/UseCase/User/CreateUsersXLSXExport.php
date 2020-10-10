<?php

declare(strict_types=1);

namespace App\UseCase\User;

use App\Domain\Model\User;
use App\UseCase\CreateXLSXExport;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use TheCodingMachine\TDBM\ResultIterator;

use function strtolower;

final class CreateUsersXLSXExport extends CreateXLSXExport
{
    /**
     * @param User[]|ResultIterator $users
     */
    public function createXLSX(string $locale, $users): Xlsx
    {
        $headerIds = [
            'users.headers.id',
            'users.headers.first_name',
            'users.headers.last_name',
            'users.headers.email',
            'users.headers.locale',
            'users.headers.role',
        ];

        $values = [];
        foreach ($users as $user) {
            $values[] = [
                $user->getId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
                $user->getLocale(),
                $this->translator->trans(
                    'users.values.roles.' . strtolower($user->getRole()),
                    [],
                    $this->getTranslationDomain(),
                    $locale,
                ),
            ];
        }

        return $this->create(
            $locale,
            $headerIds,
            $values
        );
    }
}
