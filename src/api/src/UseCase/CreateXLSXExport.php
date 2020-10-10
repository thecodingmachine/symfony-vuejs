<?php

declare(strict_types=1);

namespace App\UseCase;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Contracts\Translation\TranslatorInterface;

use function chr;

abstract class CreateXLSXExport
{
    protected TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * @param string[]       $headerIds
     * @param array<mixed[]> $values
     */
    public function create(string $locale, array $headerIds, array $values): Xlsx
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        foreach ($headerIds as $index => $headerId) {
            $sheet->setCellValue(
                $this->getExcelColumnName($index) . '1',
                $this->translator->trans(
                    $headerId,
                    [],
                    $this->getTranslationDomain(),
                    $locale
                )
            );
        }

        foreach ($values as $rowNumber => $rowValues) {
            foreach ($rowValues as $index => $value) {
                $sheet->setCellValue(
                    $this->getExcelColumnName($index) . ($rowNumber + 2),
                    $value
                );
            }
        }

        return new Xlsx($spreadsheet);
    }

    protected function getTranslationDomain(): string
    {
        return 'spreadsheets';
    }

    private function getExcelColumnName(int $columnNumber): string
    {
        $dividend   = $columnNumber + 1;
        $columnName = '';

        while ($dividend > 0) {
            $modulo     = ($dividend - 1) % 26;
            $columnName = chr(65 + $modulo) . $columnName;
            $dividend   = (int) (($dividend - $modulo) / 26);
        }

        return $columnName;
    }
}
