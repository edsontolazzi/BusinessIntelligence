<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Service\GraphData\General;

use Tolazzi\BusinessIntelligence\Model\Command\General\GetMostPopularitySearchTerms;

class MostPopularitySearchTerms
{
    /**
     * MostPopularitySearchTerms constructor.
     *
     * @param GetMostPopularitySearchTerms $getMostPopularitySearchTerms
     */
    public function __construct(
        private readonly GetMostPopularitySearchTerms $getMostPopularitySearchTerms
    ) {
    }

    /**
     * GetChartJsData method
     *
     * @return array
     */
    public function getChartJsData(): array
    {
        $orderByStateData = $this->getMostPopularitySearchTerms->execute();

        $labelsData = [];
        $datasetData = [];

        foreach ($orderByStateData as $item) {
            $labelsData[] = $item[GetMostPopularitySearchTerms::QUERY_TEXT_COLUMN];
            $datasetData[] = $item[GetMostPopularitySearchTerms::POPULARITY];
        }

        $labels = $labelsData;

        $datasetValue = [
            'label' => __('Top Search Terms'),
            'data' => $datasetData,
            'backgroundColor' => ['#1E5066', '#3178A6', '#3C94CC', '#44A6E6', '#47AFF2']
        ];

        return [
            'labels' => $labels,
            'datasets' => [
                $datasetValue
            ]
        ];
    }
}
