<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Customer;

use Magento\Framework\App\ResourceConnection;

class GetAverageOrdersByCustomers
{
    /**
     * GetAverageOrdersByCustomers constructor.
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
            ->from('sales_order', [
                'COUNT(*) AS order_count']
            )
            ->join('sales_invoice', 'sales_order.entity_id = sales_invoice.order_id', '')
            ->where('sales_order.created_at BETWEEN ' . "'$from' AND '$to'")
            ->where('sales_invoice.state = 2')
            ->group('customer_id');

        $query = $connection->select()->from($subQuery, 'AVG(order_count) AS average_orders_per_customer');

        $result = $connection->fetchRow($query);

        $average = (float) $result['average_orders_per_customer'] ?? 0;

        return round($average, 2);
    }
}
