<?php

namespace Aclips\Report\Type;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Html;

/**
 * Базовый класс для XLSX отчётов
 */
abstract class BaseXlsxReport extends BaseReport
{

    const DOCUMENT_FORMAT = 'xlsx';

    const DATA_APPLICATION = 'data:application/vnd.ms-excel;base64,';

    protected Spreadsheet $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        parent::__construct();
    }

    public function makeResultFile(string $outputFile): string
    {
        $this->saveResultFile($this->spreadsheet, $outputFile);

        return $outputFile;
    }

    public function makeHtmlFile(string $outputFile): string
    {
        $this->saveHtmlFile($this->spreadsheet, $outputFile);

        return $outputFile;
    }

    protected function saveResultFile(Spreadsheet $spreadsheet, string $outputFile)
    {
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($outputFile);
    }

    protected function saveHtmlFile(Spreadsheet $spreadsheet, string $outputFile)
    {
        $writer = new Html($spreadsheet);
        $writer->save($outputFile);
    }

    /**
     * CSS для отображения html содерджимого документа на странице
     * @return string|null
     */
    public function getCustomStyleHtml(): ?string
    {
        return "<style> 
             html {background: transparent !important;} 
             #report-wrapper table{width: 100%;}
             #report-wrapper table td{
                font-size: 12px !important;
             }
        </style>";
    }
}