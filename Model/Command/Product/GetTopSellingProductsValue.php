<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Product;

use Magento\Framework\App\ResourceConnection;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;

class GetTopSellingProductsValue
{
    /**
     * GetTopSellingProductsValue constructor.
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
            ->from('sales_order_item', [
                    'sales_order_item.product_id',
                    'catalog_product_entity.sku AS product_sku',
                    'SUM(sales_order_item.row_total) AS total_value']
            )
            ->join('catalog_product_entity',
                'sales_order_item.product_id = catalog_product_entity.entity_id',
                ''
            )
            ->join('sales_order',
                'sales_order_item.order_id = sales_order.entity_id',
                ''
            )
            ->join('sales_invoice',
                'sales_order.entity_id = sales_invoice.order_id',
                ''
            )
            ->where('sales_order.created_at BETWEEN ' . "'$from' AND '$to'")
            ->where('sales_invoice.state = 2')
            ->group(['sales_order_item.product_id', 'catalog_product_entity.sku'])
            ->order('total_value DESC')
            ->limit($this->config->getProductTopInformationRowTableNumber());

        $result = $connection->fetchAll($query);

        return $result ?? [];
    }
}
