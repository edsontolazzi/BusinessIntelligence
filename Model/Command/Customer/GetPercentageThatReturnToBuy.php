<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Customer;

use Magento\Framework\App\ResourceConnection;

class GetPercentageThatReturnToBuy
{
    /**
     * GetPercentageThatReturnToBuy constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * Execute method
     *
     * @param string $from
     * @param string $to
     *
     * @return float
     */
    public function execute(string $from, string $to): float
    {
        $connection = $this->resourceConnection->getConnection();

        $subQuery = $connection->select()
            ->from('sales_order', 'sales_order.customer_id')
            ->join('sales_invoice', 'sales_order.entity_id = sales_invoice.order_id', '')
            ->where('sales_order.created_at BETWEEN ' . "'$from' AND '$to'")
            ->where('sales_invoice.state = 2')
            ->group('sales_order.customer_id')
            ->having('COUNT(*) > 1');

        $subQuery2 = $connection->select()
            ->from('sales_order', 'COUNT(DISTINCT customer_id)')
            ->join('sales_invoice', 'sales_order.entity_id = sales_invoice.order_id', '')
            ->where('sales_order.created_at BETWEEN ' . "'$from' AND '$to'")
            ->where('sales_invoice.state = 2');

        $query = $connection->select()->from($subQuery, "(COUNT(*) * 100 / ($subQuery2)) AS return_percentage");

        $result = $connection->fetchRow($query);

        $average = (float) $result['return_percentage'] ?? 0;

        return round($average, 2);
    }
}
