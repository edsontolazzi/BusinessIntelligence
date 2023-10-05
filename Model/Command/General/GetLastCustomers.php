<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\General;

use Magento\Framework\App\ResourceConnection;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;

class GetLastCustomers
{
    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;
    /** @var Config */
    private Config $config;

    /**
     * GetLastCustomers constructor.
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
            ->from('customer_entity', [
                    'customer_entity.entity_id AS customer_id',
                    'customer_entity.firstname',
                    'customer_entity.email']
            )
            ->order('customer_entity.created_at DESC')
            ->limit($this->config->getGeneralLastInformationRowTableNumber());

        $result = $connection->fetchAll($query);

        return $result ?? [];
    }
}
