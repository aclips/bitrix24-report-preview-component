<?php

namespace Aclips\Report\Type;

/**
 * Пример отчёта
 */
class TestReport extends BaseXlsxReport
{
    const BASE_NAME = 'Тестовый отчёт';

    protected function getData(array $filter = []): array
    {
        $data = [];

        $data['SOME_LIST'] = $filter['SOME_LIST'];

        return $data;
    }

    public function generateDocument(): bool
    {
        if (!$this->validateFilter($this->filter)) {
            return false;
        }

        $data = $this->getData($this->filter);

        $sheet = $this->spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Объединенные ячейки');
        $sheet->mergeCells("A1:B2");

        $i = 3;

        foreach ($data['SOME_LIST'] as $value) {
            $sheet->setCellValue('A' . $i, 'Выбрано');
            $sheet->setCellValue('B' . $i, $value);
            $sheet->setCellValue('C' . $i, '');
            $i++;
        }

        return true;
    }

    public function getFilterParams(): ?array
    {
        return [
            'FILTER' =>
                [
                    [
                        'id' => 'SOME_LIST',
                        'name' => 'Список отображаемых значений',
                        'default' => true,
                        'type' => 'list',
                        'items' => [
                            1 => 1,
                            2 => 2,
                            3 => 3,
                        ],
                        'params' => [
                            'multiple' => 'Y'
                        ],
                        'require' => true
                    ],
                ]
        ];
    }
}