<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Sales;

use Magento\Framework\App\ResourceConnection;

class GetNumberOfOrders
{
    /**
     * GetNumberOfOrders constructor.
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
     * @return int
     */
    public function execute(string $from, string $to): int
    {
        $connection = $this->resourceConnection->getConnection();

        $query = $connection->select()->from('sales_order', 'COUNT(sales_order.entity_id) as number_orders')
            ->where('sales_order.created_at BETWEEN ' . "'$from' AND '$to'");

        $result = $connection->fetchRow($query);

        return (int) $result['number_orders'] ?? 0;
    }
}
