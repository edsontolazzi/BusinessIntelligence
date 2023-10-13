<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tolazzi\BusinessIntelligence\Model\Command\Customer\GetAverageOrdersByCustomers;
use Tolazzi\BusinessIntelligence\Model\Command\Customer\GetAverageSpentByCustomers;
use Tolazzi\BusinessIntelligence\Model\Command\Customer\GetNewCustomers;
use Tolazzi\BusinessIntelligence\Model\Command\Customer\GetPercentageThatReturnToBuy;
use Tolazzi\BusinessIntelligence\Model\Command\Customer\GetTotalCustomers;
use Tolazzi\BusinessIntelligence\Model\Command\Customer\GetUniqueSessionNumber;
use Tolazzi\BusinessIntelligence\Model\Command\GetDateParam;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;

class BiCustomersDataProvider implements ArgumentInterface
{
    /**
     * BiCustomersDataProvider constructor.
     *
     * @param GetDateParam $getDateParam
     * @param GetTotalCustomers $getTotalCustomers
     * @param GetNewCustomers $getNewCustomers
     * @param GetAverageSpentByCustomers $getAverageSpentByCustomers
     * @param GetAverageOrdersByCustomers $getAverageOrdersByCustomers
     * @param GetPercentageThatReturnToBuy $getPercentageThatReturnToBuy
     * @param GetUniqueSessionNumber $getUniqueSessionNumber
     * @param Config $config
     */
    public function __construct(
        private readonly GetDateParam $getDateParam,
        private readonly GetTotalCustomers $getTotalCustomers,
        private readonly GetNewCustomers $getNewCustomers,
        private readonly GetAverageSpentByCustomers $getAverageSpentByCustomers,
        private readonly GetAverageOrdersByCustomers $getAverageOrdersByCustomers,
        private readonly GetPercentageThatReturnToBuy $getPercentageThatReturnToBuy,
        private readonly GetUniqueSessionNumber $getUniqueSessionNumber,
        private readonly Config $config
    ) {
    }

    /**
     * GetData method
     *
     * @return array
     */
    public function getData(): array
    {
        if (!$this->isModuleEnabled() || !$this->isCustomerKPIEnabled()) {
            return [];
        }

        $dateParams = $this->getDateParam->execute();
        $from = $dateParams['from'];
        $to = $dateParams['to'];

        return [
            'total_customers' => $this->getTotalCustomers->execute($to),
            'new_customers' => $this->getNewCustomers->execute($from, $to),
            'average_spent' => $this->getAverageSpentByCustomers->execute($from, $to),
            'average_orders' => $this->getAverageOrdersByCustomers->execute($from, $to),
            'percentage_return_buy' => $this->getPercentageThatReturnToBuy->execute($from, $to),
            'single_accesses' => $this->getUniqueSessionNumber->execute($from, $to)
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
     * IsCustomerKPIEnabled method
     *
     * @return bool
     */
    public function isCustomerKPIEnabled(): bool
    {
        return $this->config->isCustomerKPIEnabled();
    }

    /**
     * GetKPIConfigs method
     *
     * @return array
     */
    public function getKPIConfigs(): array
    {
        return $this->config->getCustomerActiveKPIs();
    }
}
