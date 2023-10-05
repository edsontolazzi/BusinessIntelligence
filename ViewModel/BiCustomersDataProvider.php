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
    /** @var GetDateParam */
    private GetDateParam $getDateParam;
    /** @var GetNewCustomers */
    private GetNewCustomers $getNewCustomers;
    /** @var GetAverageSpentByCustomers */
    private GetAverageSpentByCustomers $getAverageSpentByCustomers;
    /** @var GetAverageOrdersByCustomers */
    private GetAverageOrdersByCustomers $getAverageOrdersByCustomers;
    /** @var GetTotalCustomers */
    private GetTotalCustomers $getTotalCustomers;
    /** @var GetPercentageThatReturnToBuy */
    private GetPercentageThatReturnToBuy $getPercentageThatReturnToBuy;
    /** @var GetUniqueSessionNumber */
    private GetUniqueSessionNumber $getUniqueSessionNumber;
    /** @var Config */
    private Config $config;

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
        GetDateParam $getDateParam,
        GetTotalCustomers $getTotalCustomers,
        GetNewCustomers $getNewCustomers,
        GetAverageSpentByCustomers $getAverageSpentByCustomers,
        GetAverageOrdersByCustomers $getAverageOrdersByCustomers,
        GetPercentageThatReturnToBuy $getPercentageThatReturnToBuy,
        GetUniqueSessionNumber $getUniqueSessionNumber,
        Config $config
    ) {
        $this->config = $config;
        $this->getUniqueSessionNumber = $getUniqueSessionNumber;
        $this->getPercentageThatReturnToBuy = $getPercentageThatReturnToBuy;
        $this->getTotalCustomers = $getTotalCustomers;
        $this->getAverageOrdersByCustomers = $getAverageOrdersByCustomers;
        $this->getAverageSpentByCustomers = $getAverageSpentByCustomers;
        $this->getNewCustomers = $getNewCustomers;
        $this->getDateParam = $getDateParam;
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
