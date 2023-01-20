<?php

namespace Aclips\Components;

use Bitrix\Main\Engine\Contract\Controllerable;
use Aclips\Report\Type\ReportInterface;
use Aclips\Report\ReportManager;
use Bitrix\Main\Errorable;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\ErrorableImplementation;

class ReportDetailComponent extends \CBitrixComponent implements Controllerable, Errorable
{
    use ErrorableImplementation;

    protected ReportManager $reportManager;

    public function __construct($component = null)
    {
        $this->reportManager = new ReportManager();
        parent::__construct($component);
    }

    public function configureActions()
    {
        return [];
    }

    public function listKeysSignedParameters()
    {
        return [
            'CODE'
        ];
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $report = $this->getReport($this->arParams['CODE']);

        if (!$report) {
            showError('Отчёт не найден');
            return false;
        }

        $APPLICATION->setTitle($report->getName());

        $this->arResult['REPORT_ID'] = $report->getReportId();
        $this->arResult['FILTER_PARAMS'] = $report->getFilterParams();
        $this->arResult['CUSTOM_STYLE'] = $report->getCustomStyleHtml();

        $this->arResult['SIGNED_PARAMETERS'] = $this->getSignedParameters();

        $this->includeComponentTemplate();
    }

    /**
     * Подготовка фильтра
     * @param int $filterId
     * @param array $filter
     * @return array
     */
    public function prepareFilter(string $filterId, array $filter): array
    {
        $filterOption = new \Bitrix\Main\UI\Filter\Options($filterId);
        $filterData = $filterOption->getFilter([]);

        $filterPrepared = \Bitrix\Main\UI\Filter\Type::getLogicFilter($filterData, $filter);

        // hack for dates
        $dateFields = array_filter($filter, static function ($e) {
            return $e['type'] == 'date';
        });

        foreach ($dateFields as $dateField) {
            $id = $dateField['id'];

            if (!empty($filterData[$id . '_from'])) {
                $filterPrepared['>=' . $id] = $filterData[$id . '_from'];
            }

            if (!empty($filterData[$id . '_to'])) {
                $filterPrepared['<=' . $id] = $filterData[$id . '_to'];
            }
        }

        return $filterPrepared;
    }

    public function applyFilterAction()
    {
        $this->errorCollection = new ErrorCollection();

        $result = new \Bitrix\Main\Result();

        $report = $this->getReport($this->arParams['CODE']);

        if (!$report) {
            $result->addError(new Error('Отчёт не найден'));
            $this->errorCollection->add($result->getErrors());

            return $result;
        }

        $filterParams = $report->getFilterParams();
        $filterId = $report->getReportId();
        $filterFields = $filterParams['FILTER'];

        if (!empty($filterFields)) {
            $filter = $this->prepareFilter($filterId, $filterFields);
            $report->setFilter($filter);
        }

        if ($report->generateDocument()) {
            $reportHtml = clone $report;

            $resultFilePath = $report->makeResultFile($this->generateFilePath());
            $resultContent = file_get_contents($resultFilePath);

            $htmlFilePath = $reportHtml->makeHtmlFile($resultFilePath);
            $htmlContent = file_get_contents($htmlFilePath);

            $result->setData([
                'file_name' => $report->getName(),
                'file_format' => $report->getReportFormat(),
                'result' => $report->getDataApplication() . base64_encode($resultContent),
                'html' => $htmlContent,
            ]);
        } else {
            $errors = $report->getErrors();

            foreach ($errors as $error) {
                $result->addError(new Error($error));
            }

            $this->errorCollection->add($result->getErrors());
        }

        return $result->getData();
    }

    public function getReport(string $code): ?ReportInterface
    {
        $reportCode = trim($code);
        return $this->reportManager->getReportByCode($reportCode);
    }

    /**
     * Получение пути для сохранения файла
     * @param string|null $dir
     * @param string|null $name
     * @return string
     */
    private function generateFilePath(?string $dir = null, ?string $name = null): string
    {
        if (empty($dir)) {
            $dir = sys_get_temp_dir();
        }

        if (empty($name)) {
            $name = uniqid();
        }

        return "{$dir}/{$name}";
    }
}