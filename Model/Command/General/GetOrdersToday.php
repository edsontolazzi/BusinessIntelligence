<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\General;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Tolazzi\BusinessIntelligence\Model\DateUtil;

class GetOrdersToday
{
    public const TOTAL_ORDERS_COLUMN = 'total_orders';
    public const TOTAL_VALUE_COLUMN = 'total_value';

    /**
     * GetLastOrders constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param DateUtil $dateUtil
     */
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly DateUtil $dateUtil
    ) {
    }

    /**
     * Execute method
     *
     * @return array
     *
     * @throws Exception
     */
    public function execute(): array
    {
        $connection = $this->resourceConnection->getConnection();

        $today = $this->dateUtil->getDateToday('Y-m-d');
        $tomorrow = $this->dateUtil->addDays(1, $today);

        $query = $connection
            ->select()
            ->from('sales_order', [
                    'COUNT(*) AS total_orders',
                    'SUM(grand_total) AS total_value']
            )
            ->where('sales_order.created_at BETWEEN ' . "'$today' AND '$tomorrow'");

        $result = $connection->fetchRow($query);

        return [
            self::TOTAL_ORDERS_COLUMN => $result[self::TOTAL_ORDERS_COLUMN] ?? 0,
            self::TOTAL_VALUE_COLUMN => $result[self::TOTAL_VALUE_COLUMN] ?? 0
        ];
    }
}
