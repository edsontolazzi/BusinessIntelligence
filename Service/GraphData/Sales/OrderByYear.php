<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Service\GraphData\Sales;

use Tolazzi\BusinessIntelligence\Model\Command\Sales\GetOrderByYear;

class OrderByYear
{
    /**
     * OrderByYear constructor.
     *
     * @param GetOrderByYear $getOrderByYear
     */
    public function __construct(
        private readonly GetOrderByYear $getOrderByYear
    ) {
    }

    /**
     * GetChartJsData method
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function getChartJsData(string $from, string $to): array
    {
        $orderByYearData = $this->getOrderByYear->execute($from, $to);

        $labelsData = [];
        $datasetValueData = [];
        $datasetNumberData = [];

        foreach ($orderByYearData as $item) {
            $labelsData[] = $item[GetOrderByYear::YEAR_COLUMN];
            $datasetValueData[] = $item[GetOrderByYear::SALES_TOTAL_VALUE_COLUMN];
            $datasetNumberData[] = $item[GetOrderByYear::SALES_NUMBER_COLUMN];
        }

        $labels = $labelsData;

        $datasetValue = [
            'label' => __('Orders By Year (Value)'),
            'data' => $datasetValueData,
            'backgroundColor' => ['#1E5066']
        ];

        $datasetNumber = [
            'label' => __('Orders By Year (Number)'),
            'data' => $datasetNumberData,
            'backgroundColor' => ['#1E5066']

        ];

        return [
            'order_by_year_number' => [
                'labels' => $labels,
                'datasets' => [
                    $datasetNumber
                ]
            ],
            'order_by_year_value' => [
                'labels' => $labels,
                'datasets' => [
                    $datasetValue
                ]
            ],
        ];
    }
}
