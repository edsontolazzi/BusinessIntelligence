<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\General;

use Magento\Framework\App\ResourceConnection;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;

class GetAbandonedCarts
{
    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;
    /** @var Config */
    private Config $config;

    /**
     * GetAbandonedCarts constructor.
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
     * @return int
     */
    public function execute(): int
    {
        $connection = $this->resourceConnection->getConnection();

        $query = $connection->select()->from('quote', ['COUNT(*) AS abandoned_carts'])
            ->where('is_active = 1')
            ->where('items_count > 0')
            ->where('customer_email IS NOT NULL')
            ->where('updated_at < NOW() - INTERVAL '. $this->config->getGeneralTimeToAbandonedCart() .' HOUR');

        $result = $connection->fetchRow($query);

        return (int) $result['abandoned_carts'] ?? 0;
    }
}
