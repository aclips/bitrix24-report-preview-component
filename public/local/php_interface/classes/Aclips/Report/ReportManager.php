<?php

namespace Aclips\Report;

use Aclips\Report\Type\ReportInterface;

/**
 * Менеджер отчётов
 */
class ReportManager
{
    public function __construct()
    {

    }

    /**
     * Получение отчёта по коду
     * Кодом является название класса отчёта без суффикса 'Report'
     * @param string $code
     * @return ReportInterface|null
     */
    public function getReportByCode(string $code): ?ReportInterface
    {
        $code = ucfirst($code);

        $reportClassName = '\\Aclips\\Report\\Type\\' . $code . 'Report';

        if (class_exists($reportClassName)) {
            try {
                $reportClass = new $reportClassName();
                return $reportClass;
            } catch (\Throwable $e) {
                //@TODO обработка ошибки
            }
        }

        return null;
    }

}
