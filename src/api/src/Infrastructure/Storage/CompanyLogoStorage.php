<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Domain\Model\Storable\CompanyLogo;
use App\Domain\Store\CompanyLogoStore;
use App\Domain\Throwable\Invalid\InvalidCompanyLogo;
use League\Flysystem\FilesystemInterface;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CompanyLogoStorage implements CompanyLogoStore
{
    private ValidatorInterface $validator;
    private FilesystemInterface $uploadsStorage;

    public function __construct(ValidatorInterface $validator, FilesystemInterface $uploadsStorage)
    {
        $this->validator      = $validator;
        $this->uploadsStorage = $uploadsStorage;
    }

    /**
     * @throws InvalidCompanyLogo
     */
    public function put(CompanyLogo $logo) : string
    {
        $violations = $this->validator->validate($logo);
        InvalidCompanyLogo::throwException($violations);

        $result = $this->uploadsStorage->putStream(
            'company/' . $logo->getGeneratedFileName(),
            $logo->getResource()
        );

        if ($result !== false) {
            return $logo->getGeneratedFileName();
        }

        throw new RuntimeException(
            'Failed to store company logo "' .
            $logo->getGeneratedFileName() .
            '"'
        );
    }

    public function delete(string $fileName) : void
    {
        // TODO: Implement delete() method.
    }
}
