<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\ViewModel;

use Exception;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;
use Tolazzi\BusinessIntelligence\Model\Command\General\GetAbandonedCarts;
use Tolazzi\BusinessIntelligence\Model\Command\General\GetLastCustomers;
use Tolazzi\BusinessIntelligence\Model\Command\General\GetLastOrders;
use Tolazzi\BusinessIntelligence\Model\Command\General\GetNewCustomersToday;
use Tolazzi\BusinessIntelligence\Model\Command\General\GetOrdersToday;
use Tolazzi\BusinessIntelligence\Model\Command\General\GetTopBiggestOrders;
use Tolazzi\BusinessIntelligence\Model\Command\General\GetTopBuyers;
use Tolazzi\BusinessIntelligence\Service\GraphData\General\MostPopularitySearchTerms;

class BiGeneralDataProvider implements ArgumentInterface
{
    /**
     * BiGeneralDataProvider constructor.
     *
     * @param Json $jsonSerializer
     * @param GetLastOrders $getLastOrders
     * @param GetLastCustomers $getLastCustomers
     * @param MostPopularitySearchTerms $mostPopularitySearchTerms
     * @param GetOrdersToday $getOrdersToday
     * @param GetNewCustomersToday $getNewCustomersToday
     * @param GetAbandonedCarts $getAbandonedCarts
     * @param GetTopBiggestOrders $getTopBiggestOrders
     * @param GetTopBuyers $getTopBuyers
     * @param Config $config
     */
    public function __construct(
        private readonly Json $jsonSerializer,
        private readonly GetLastOrders $getLastOrders,
        private readonly GetLastCustomers $getLastCustomers,
        private readonly MostPopularitySearchTerms $mostPopularitySearchTerms,
        private readonly GetOrdersToday $getOrdersToday,
        private readonly GetNewCustomersToday $getNewCustomersToday,
        private readonly GetAbandonedCarts $getAbandonedCarts,
        private readonly GetTopBiggestOrders $getTopBiggestOrders,
        private readonly GetTopBuyers $getTopBuyers,
        private readonly Config $config
    ) {
    }

    /**
     * GetData method
     *
     * @return array
     *
     * @throws Exception
     */
    public function getData(): array
    {
        if (!$this->isModuleEnabled() || !$this->isGeneralKPIEnabled()) {
            return [];
        }

        $topSearchTermsData = $this->mostPopularitySearchTerms->getChartJsData();
        $ordersTodayData = $this->getOrdersToday->execute();

        return [
            'last_orders' => $this->getLastOrders->execute(),
            'last_customers' => $this->getLastCustomers->execute(),
            'top_orders' => $this->getTopBiggestOrders->execute(),
            'top_buyers' => $this->getTopBuyers->execute(),
            'new_customers' => $this->getNewCustomersToday->execute(),
            'abandoned_carts' => $this->getAbandonedCarts->execute(),
            'total_orders' => $ordersTodayData[GetOrdersToday::TOTAL_ORDERS_COLUMN],
            'total_value' => $ordersTodayData[GetOrdersToday::TOTAL_VALUE_COLUMN],
            'top_search_terms' => $this->jsonSerializer->serialize($topSearchTermsData),
        ];
    }

    /**
     * IsModuleEnabled method
     *
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->config->isModuleEnabled();
    }

    /**
     * IsGeneralKPIEnabled method
     *
     * @return bool
     */
    public function isGeneralKPIEnabled(): bool
    {
        return $this->config->isGeneralKPIEnabled();
    }

    /**
     * GetKPIConfigs method
     *
     * @return array
     */
    public function getKPIConfigs(): array
    {
        return $this->config->getGeneralActiveKPIs();
    }
}
