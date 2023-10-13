<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\ViewModel;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tolazzi\BusinessIntelligence\Model\Command\GetDateParam;
use Tolazzi\BusinessIntelligence\Model\Command\Sales\GetNumberOfInvoicedOrders;
use Tolazzi\BusinessIntelligence\Model\Command\Sales\GetNumberOfOrders;
use Tolazzi\BusinessIntelligence\Model\Command\Sales\GetTotalInvoiced;
use Tolazzi\BusinessIntelligence\Model\Command\Sales\GetTotalOrdered;
use Tolazzi\BusinessIntelligence\Model\Command\Sales\GetValueAndNumberOfNotPaidInvoices;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;
use Tolazzi\BusinessIntelligence\Service\GraphData\Sales\OrderByMonth;
use Tolazzi\BusinessIntelligence\Service\GraphData\Sales\OrderByState;
use Tolazzi\BusinessIntelligence\Service\GraphData\Sales\OrderByYear;

class BiSalesDataProvider implements ArgumentInterface
{
    /**
     * BiSalesDataProvider constructor.
     *
     * @param GetDateParam $getDateParam
     * @param Json $jsonSerializer
     * @param GetTotalInvoiced $getTotalInvoiced
     * @param GetNumberOfInvoicedOrders $getNumberOfInvoicedOrders
     * @param GetNumberOfOrders $getNumberOfOrders
     * @param OrderByState $orderByState
     * @param OrderByMonth $orderByMonth
     * @param GetValueAndNumberOfNotPaidInvoices $getValueAndNumberOfNotPaidInvoices
     * @param GetTotalOrdered $getTotalOrdered
     * @param OrderByYear $orderByYear
     * @param Config $config
     */
    public function __construct(
        private readonly GetDateParam $getDateParam,
        private readonly Json $jsonSerializer,
        private readonly GetTotalInvoiced $getTotalInvoiced,
        private readonly GetNumberOfInvoicedOrders $getNumberOfInvoicedOrders,
        private readonly GetNumberOfOrders $getNumberOfOrders,
        private readonly OrderByState $orderByState,
        private readonly OrderByMonth $orderByMonth,
        private readonly GetValueAndNumberOfNotPaidInvoices $getValueAndNumberOfNotPaidInvoices,
        private readonly GetTotalOrdered $getTotalOrdered,
        private readonly OrderByYear $orderByYear,
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
        if (!$this->isModuleEnabled() || !$this->isSalesKPIEnabled()) {
            return [];
        }

        $dates = $this->getDateParam->execute();
        $from = $dates['from'];
        $to = $dates['to'];

        $orderByStateNumber = $this->getDataOrderByState($from, $to)['order_by_state_number'];
        $orderByStateValue = $this->getDataOrderByState($from, $to)['order_by_state_value'];

        $orderByMonthNumber = $this->getDataOrderByMonth($from, $to)['order_by_month_number'];
        $orderByMonthValue = $this->getDataOrderByMonth($from, $to)['order_by_month_value'];

        $orderByYearValue = $this->orderByYear->getChartJsData($from, $to)['order_by_year_value'];
        $orderByYearNumber = $this->orderByYear->getChartJsData($from, $to)['order_by_year_number'];

        $ordersNotPaid = $this->getValueAndNumberOfNotPaidInvoices->execute($from, $to);

        $orderedData = $this->getNumberOfOrderValueAndTicket($from, $to);

        return [
            'total_value_invoiced' => $this->getTotalInvoiced($from, $to),
            'average_ticket' => $this->getAverageTicket($from, $to),
            'number_orders_general' => $orderedData['number_orders'],
            'total_value_ordered' => $orderedData['total_value_ordered'],
            'ticket_total_ordered' => $orderedData['ticket_total_ordered'],
            'number_orders_invoiced' => $this->getNumberOfInvoicedOrders->execute($from, $to),
            'order_by_state_number' => $this->jsonSerializer->serialize($orderByStateNumber),
            'order_by_state_value' => $this->jsonSerializer->serialize($orderByStateValue),
            'order_by_month_number' => $this->jsonSerializer->serialize($orderByMonthNumber),
            'order_by_month_value' => $this->jsonSerializer->serialize($orderByMonthValue),
            'order_by_year_value' => $this->jsonSerializer->serialize($orderByYearValue),
            'order_by_year_number' => $this->jsonSerializer->serialize($orderByYearNumber),
            'number_orders_not_paid' => $ordersNotPaid[GetValueAndNumberOfNotPaidInvoices::NUMBER_ORDERS_COLUMN],
            'value_orders_not_paid' => $ordersNotPaid[GetValueAndNumberOfNotPaidInvoices::VALUE_ORDERS_COLUMN],
        ];
    }

    /**
     * GetTotalBilling method
     *
     * @param string $from
     * @param string $to
     *
     * @return float
     */
    public function getTotalInvoiced(string $from, string $to): float
    {
        return $this->getTotalInvoiced->execute($from, $to);
    }

    /**
     * GetAverageTicket method
     *
     * @param string $from
     * @param string $to
     *
     * @return float
     */
    public function getAverageTicket(string $from, string $to): float
    {
        $totalInvoiced = $this->getTotalInvoiced->execute($from, $to);
        $numberOfInvoicedOrders = $this->getNumberOfInvoicedOrders->execute($from, $to);

        if ($numberOfInvoicedOrders == 0 || $totalInvoiced == 0) {
            return 0;
        }

        return (float) ($totalInvoiced / $numberOfInvoicedOrders) ?? 0;
    }

    /**
     * GetDataOrderByState method
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function getDataOrderByState(string $from, string $to): array
    {
        return $this->orderByState->getChartJsData($from, $to);
    }

    /**
     * GetDataOrderByMonth method
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function getDataOrderByMonth(string $from, string $to): array
    {
        return $this->orderByMonth->getChartJsData($from, $to);
    }

    /**
     * GetNumberOfOrderValueAndTicket method
     *
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function getNumberOfOrderValueAndTicket(string $from, string $to): array
    {
        $totalOfOrders = $this->getNumberOfOrders->execute($from, $to);
        $totalValueOrdered = $this->getTotalOrdered->execute($from, $to);

        if ($totalOfOrders != 0 && $totalValueOrdered != 0) {
            $averageTicket = $totalValueOrdered / $totalOfOrders;

            return [
                'number_orders' => $totalOfOrders,
                'total_value_ordered' => $totalValueOrdered,
                'ticket_total_ordered' => $averageTicket
            ];
        }

        return [
            'number_orders' => $totalOfOrders,
            'total_value_ordered' => $totalValueOrdered,
            'ticket_total_ordered' => 0
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
     * IsSalesKPIEnabled method
     *
     * @return bool
     */
    public function isSalesKPIEnabled(): bool
    {
        return $this->config->isSalesKPIEnabled();
    }

    /**
     * GetKPIConfigs method
     *
     * @return array
     */
    public function getKPIConfigs(): array
    {
        return $this->config->getSalesActiveKPIs();
    }
}
