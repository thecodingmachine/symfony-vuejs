<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

use function Safe\file_get_contents;
use function Safe\unlink;

abstract class DownloadXLSXController extends DownloadController
{
    protected function createResponseWithXLSXAttachment(string $filename, Xlsx $xlsx): Response
    {
        $tmpFilename = Uuid::uuid4()->toString() . '.xlsx';
        $xlsx->save($tmpFilename);
        $fileContent = file_get_contents($tmpFilename);
        unlink($tmpFilename);

        return $this->createResponseWithAttachment(
            $filename,
            $fileContent
        );
    }
}
