<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Service\GraphData\Sales;

use Tolazzi\BusinessIntelligence\Model\Command\Sales\GetOrderByMonth;

class OrderByMonth
{
    private const MONTHS_LABELS = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December'
    ];

    /**
     * OrderByMonth constructor.
     *
     * @param GetOrderByMonth $getOrderByMonth
     */
    public function __construct(
        private readonly GetOrderByMonth $getOrderByMonth
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
        $orderByMonthData = $this->getOrderByMonth->execute($from, $to);

        $labelsData = [];
        $datasetValueData = [];
        $datasetNumberData = [];

        foreach ($orderByMonthData as $item) {
            $labelsData[] = __(self::MONTHS_LABELS[$item[GetOrderByMonth::MONTH_COLUMN]])
                . ' ' . $item[GetOrderByMonth::YEAR_COLUMN];
            $datasetValueData[] = $item[GetOrderByMonth::SALES_TOTAL_VALUE_COLUMN];
            $datasetNumberData[] = $item[GetOrderByMonth::SALES_NUMBER_COLUMN];
        }

        $labels = $labelsData;

        $datasetValue = [
            'label' => __('Orders By Month (Value)'),
            'data' => $datasetValueData,
            'backgroundColor' => ['#1E5066']
        ];

        $datasetNumber = [
            'label' => __('Orders By Month (Number)'),
            'data' => $datasetNumberData,
            'backgroundColor' => ['#1E5066']

        ];

        return [
            'order_by_month_number' => [
                'labels' => $labels,
                'datasets' => [
                    $datasetNumber
                ]
            ],
            'order_by_month_value' => [
                'labels' => $labels,
                'datasets' => [
                    $datasetValue
                ]
            ],
        ];
    }
}
