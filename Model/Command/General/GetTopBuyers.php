<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\General;

use Magento\Framework\App\ResourceConnection;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;

class GetTopBuyers
{
    /**
     * GetTopBuyers constructor.
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
                    'sales_order.customer_id',
                    'SUM(sales_order.grand_total) AS total_spent']
            )
            ->join('customer_entity',
                'sales_order.customer_id = customer_entity.entity_id',
                ''
            )
            ->join('sales_invoice',
                'sales_invoice.order_id = sales_order.entity_id',
                ''
            )
            ->where('sales_invoice.state = 2')
            ->group(['sales_order.customer_id', 'customer_entity.firstname'])
            ->order('total_spent DESC')
            ->limit($this->config->getGeneralTopInformationRowTableNumber());

        $result = $connection->fetchAll($query);

        return $result ?? [];
    }
}
