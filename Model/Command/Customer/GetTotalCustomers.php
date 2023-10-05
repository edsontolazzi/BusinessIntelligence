<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Customer;

use Magento\Framework\App\ResourceConnection;

class GetTotalCustomers
{
    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;

    /**
     * GetTotalCustomers constructor.
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
     * @param string $to
     *
     * @return int
     */
    public function execute(string $to): int
    {
        $connection = $this->resourceConnection->getConnection();

        $query = $connection
            ->select()
            ->from('customer_entity', ['COUNT(*) AS total_customers'])
            ->where('customer_entity.created_at < ' . "'$to'");

        $result = $connection->fetchRow($query);

        return (int) $result['total_customers'] ?? 0;
    }
}
