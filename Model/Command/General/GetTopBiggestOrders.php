<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\General;

use Magento\Framework\App\ResourceConnection;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;

class GetTopBiggestOrders
{
    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;
    /** @var Config */
    private Config $config;

    /**
     * GetTopBiggestOrders constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param Config $config
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Config $config
    ) {
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
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
                    'sales_order.entity_id AS order_id',
                    'sales_order.increment_id AS order_number',
                    'sales_order.customer_id',
                    'customer_entity.firstname',
                    'sales_order.grand_total AS order_value']
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
            ->order('sales_order.grand_total DESC')
            ->limit($this->config->getGeneralTopInformationRowTableNumber());

        $result = $connection->fetchAll($query);

        return $result ?? [];
    }
}
