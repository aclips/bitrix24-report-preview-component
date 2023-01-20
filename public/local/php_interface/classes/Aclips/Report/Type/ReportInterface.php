<?php

namespace Aclips\Report\Type;

/**
 * Интерфейс для отчётов
 */
interface ReportInterface
{
    /**
     * Полученеи идентификатора отчёта
     * @return string
     */
    public function getReportId(): string;

    /**
     * Получение названия отчёта
     * @return string
     */
    public function getName(): string;

    /**
     * Установка названия отчёта
     * @param string $title
     * @return mixed
     */
    public function setTitle(string $title);

    /**
     * Установка фильтра
     * @param array $filter
     */
    public function setFilter(array $filter = []);

    /**
     * Получение формата генерируемого отчёта
     * @return string
     */
    public function getReportFormat(): string;

    /**
     * Получение заголовка для скачивания
     * @return string
     */
    public function getDataApplication(): string;

    /**
     * Генерация документа
     * @return bool
     */
    public function generateDocument(): bool;

    /**
     * Получение параметров фильтрации
     * @return array|null
     */
    public function getFilterParams(): ?array;

    /**
     * Получение css стилей для страницы отчёта
     * Возвращает строку вида <style>...</style>
     * @return string|null
     */
    public function getCustomStyleHtml(): ?string;

    /**
     * Формирование документа
     * @param string $outputFile
     * @return string
     */
    public function makeResultFile(string $outputFile): string;

    /**
     * Формирование Html документа
     * @param string $outputFile
     * @return string
     */
    public function makeHtmlFile(string $outputFile): string;
}
