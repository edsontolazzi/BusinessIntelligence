<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Sales;

use Magento\Framework\App\ResourceConnection;

class GetOrderByMonth
{
    public const YEAR_COLUMN = 'year';
    public const MONTH_COLUMN = 'month';
    public const SALES_NUMBER_COLUMN = 'sales_number';
    public const SALES_TOTAL_VALUE_COLUMN = 'sales_total_value';

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
                    'EXTRACT(YEAR FROM sales_order.created_at) AS year',
                    'EXTRACT(MONTH FROM sales_order.created_at) AS month',
                    'COUNT(sales_order.entity_id) AS sales_number',
                    'SUM(sales_order.grand_total) as sales_total_value']
            )
            ->join('sales_invoice',
                'sales_invoice.order_id = sales_order.entity_id',
                ''
            )
            ->where('sales_order.created_at BETWEEN ' . "'$from' AND '$to'")
            ->group(['EXTRACT(YEAR FROM sales_order.created_at)', 'EXTRACT(MONTH FROM sales_order.created_at)'])
            ->order(['year', 'month']);

        $result = $connection->fetchAll($query);

        return $result ?? [];
    }
}
