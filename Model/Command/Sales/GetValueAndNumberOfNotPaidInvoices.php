<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Sales;

use Magento\Framework\App\ResourceConnection;

class GetValueAndNumberOfNotPaidInvoices
{
    public const NUMBER_ORDERS_COLUMN = 'number_orders_not_paid';
    public const VALUE_ORDERS_COLUMN = 'value_orders_not_paid';

    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;

    /**
     * GetOrderByMonth constructor.
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
     * @return array
     */
    public function execute(string $from, string $to): array
    {
        $connection = $this->resourceConnection->getConnection();

        $query = $connection
            ->select()
            ->from('sales_order', [
                    'COUNT(sales_order.entity_id) AS number_orders_not_paid',
                    'SUM(sales_order.grand_total) AS value_orders_not_paid',]
            )
            ->join('sales_invoice',
                'sales_invoice.order_id = sales_order.entity_id',
                ''
            )
            ->where('sales_order.created_at BETWEEN ' . "'$from' AND '$to'")
            ->where('sales_invoice.state = 1');

        $result = $connection->fetchRow($query);

        return [
            self::NUMBER_ORDERS_COLUMN => $result[self::NUMBER_ORDERS_COLUMN] ?? 0,
            self::VALUE_ORDERS_COLUMN => $result[self::VALUE_ORDERS_COLUMN] ?? 0
        ];
    }
}
