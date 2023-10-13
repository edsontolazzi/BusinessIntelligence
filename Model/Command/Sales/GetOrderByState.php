<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Sales;

use Magento\Framework\App\ResourceConnection;

class GetOrderByState
{
    public const REGION_COLUMN = 'region';
    public const SALES_NUMBER_COLUMN = 'sales_number';
    public const SALES_VALUE_COLUMN = 'sales_value';

    /**
     * GetOrderByState constructor.
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
     * @return array
     */
    public function execute(string $from, string $to): array
    {
        $connection = $this->resourceConnection->getConnection();

        $query = $connection
            ->select()
            ->from('sales_order', [
                'sales_order_address.region',
                'COUNT(DISTINCT sales_order.entity_id) as sales_number',
                'SUM(sales_order.grand_total) as sales_value']
            )
            ->join('sales_order_address',
                "sales_order_address.parent_id = sales_order.entity_id AND sales_order_address.address_type = 'shipping'",
                ''
            )
            ->join('sales_invoice',
                'sales_invoice.order_id = sales_order.entity_id',
                ''
            )
            ->where('sales_order.created_at BETWEEN ' . "'$from' AND '$to'")
            ->group('sales_order_address.region');

        $result = $connection->fetchAll($query);

        return $result ?? [];
    }
}
