<?php

namespace Aclips\Report\Type;

/**
 * Базовый класс для отчётов
 */
abstract class BaseReport implements ReportInterface
{
    /**
     * Базовое название отчёта
     */
    const BASE_NAME = 'отчёт';

    /**
     * Расширение файла (для скачивания)
     */
    const DOCUMENT_FORMAT = '';

    /**
     * Заголовок документа (для скачивания)
     */
    const DATA_APPLICATION = '';

    /**
     * Произвольное название отчёта
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * Параметры фильтрации
     * @var array
     */
    protected array $filter = [];

    /**
     * Массив ошибок
     * @var array
     */
    protected array $errors = [];

    public function __construct()
    {

    }

    /**
     * Установка ошибки
     * @param string $error
     * @return void
     */
    protected function setError(string $error)
    {
        $this->errors[] = $error;
    }

    /**
     * Получение массива ошибок
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Установка произвольного названия отчёта
     * @param string|null $title
     * @return void
     */
    public function setTitle(?string $title = null)
    {
        $this->title = $title;
    }

    /**
     * Установка параметров фильтрации
     * @param array $filter
     * @return void
     */
    public function setFilter(array $filter = [])
    {
        $this->filter = $filter;
    }

    /**
     * Получение названия отчёта
     * @return string
     */
    public function getName(): string
    {
        return $this->title ?? static::BASE_NAME;
    }

    /**
     * Получение полей для отображения в поисковом фильтре
     * @return array|null
     */
    public function getFilterParams(): ?array
    {
        return null;
    }

    public function getReportFormat(): string
    {
        return static::DOCUMENT_FORMAT;
    }

    public function getDataApplication(): string
    {
        return static::DATA_APPLICATION;
    }

    public function getReportId(): string
    {
        return strtoupper(str_replace('\\', '_', static::class));
    }

    /**
     * Валидация фильтра (проверка на допустимые значения)
     * В данном классе проверяется заполненность обязательных параметров фильтрации
     * @param array $filter
     * @return bool
     */
    protected function validateFilter(array $filter = []): bool
    {
        $isValid = true;

        $reportFilterParams = $this->getFilterParams();

        if (!empty($reportFilterParams)) {

            $requiredFilterFields = array_filter($reportFilterParams['FILTER'], function ($f) {
                return $f['require'] == true;
            });

            $filter = array_filter($filter);

            $filterKeys = array_unique(array_map(function ($k) {
                return str_replace(['>', '<', '='], '', $k);
            }, array_keys($filter)));

            foreach ($requiredFilterFields as $requiredFilterField) {
                if (!in_array($requiredFilterField['id'], $filterKeys)) {
                    $this->setError('Не указан параметр в фильтре "' . $requiredFilterField['name'] . '"');
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }

    public function getCustomStyleHtml(): ?string
    {
        return null;
    }
}
