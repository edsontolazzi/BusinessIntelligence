<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\General;

use Magento\Framework\App\ResourceConnection;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;

class GetLastOrders
{
    /**
     * GetLastOrders constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param Config $config
     */
    public function __construct(
        private readonly ResourceConnection $resourceConnection,
        private readonly Config $config
    ) {
    }

    /**
     * Execute method
     *
     * @return array
     */
    public function execute(): array
    {
        $connection = $this->resourceConnection->getConnection();

        $query = $connection
            ->select()
            ->from('sales_order', [
                    'customer_entity.firstname',
                    'sales_order.increment_id',
                    'customer_entity.entity_id AS customer_id',
                    'sales_order.grand_total AS order_value']
            )
            ->join('customer_entity',
                'sales_order.customer_id = customer_entity.entity_id',
                ''
            )
            ->order('sales_order.created_at DESC')
            ->limit($this->config->getGeneralLastInformationRowTableNumber());

        $result = $connection->fetchAll($query);

        return $result ?? [];
    }
}
