<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Sales;

use Magento\Framework\App\ResourceConnection;

class GetTotalOrdered
{
    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;

    /**
     * GetTotalOrdered constructor.
     *
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
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

        $query = $connection->select()->from('sales_order', 'SUM(sales_order.grand_total) as total_number_ordered')
            ->where('sales_order.created_at BETWEEN ' . "'$from' AND '$to'");

        $result = $connection->fetchRow($query);

        return (float) $result['total_number_ordered'] ?? 0;
    }
}
