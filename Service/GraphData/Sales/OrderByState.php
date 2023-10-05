<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Service\GraphData\Sales;

use Tolazzi\BusinessIntelligence\Model\Command\Sales\GetOrderByState;

class OrderByState
{
    /** @var GetOrderByState */
    private GetOrderByState $getOrderByState;

    /**
     * OrderByState constructor.
     *
     * @param GetOrderByState $getOrderByState
     */
    public function __construct(
        GetOrderByState $getOrderByState
    ) {
        $this->getOrderByState = $getOrderByState;
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
        $orderByStateData = $this->getOrderByState->execute($from, $to);

        $labelsData = [];
        $datasetValueData = [];
        $datasetNumberData = [];

        foreach ($orderByStateData as $item) {
            $labelsData[] = $item[GetOrderByState::REGION_COLUMN];
            $datasetValueData[] = $item[GetOrderByState::SALES_VALUE_COLUMN];
            $datasetNumberData[] = $item[GetOrderByState::SALES_NUMBER_COLUMN];
        }

        $labels = $labelsData;

        $datasetValue = [
            'label' => __('Orders By State (Value)'),
            'data' => $datasetValueData,
            'backgroundColor' => ['#1E5066']
        ];

        $datasetNumber = [
            'label' => __('Orders By State (Number)'),
            'data' => $datasetNumberData,
            'backgroundColor' => ['#1E5066']

        ];

        return [
            'order_by_state_number' => [
                'labels' => $labels,
                'datasets' => [
                    $datasetNumber
                ]
            ],
            'order_by_state_value' => [
                'labels' => $labels,
                'datasets' => [
                    $datasetValue
                ]
            ],
        ];
    }
}
