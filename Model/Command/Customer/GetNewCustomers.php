<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\Customer;

use Magento\Framework\App\ResourceConnection;

class GetNewCustomers
{
    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;

    /**
     * GetNewCustomers constructor.
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
     * @return int
     */
    public function execute(string $from, string $to): int
    {
        $connection = $this->resourceConnection->getConnection();

        $query = $connection
            ->select()
            ->from('customer_entity', ['COUNT(*) AS new_customers'])
            ->where('customer_entity.created_at BETWEEN ' . "'$from' AND '$to'");

        $result = $connection->fetchRow($query);

        return (int) $result['new_customers'] ?? 0;
    }
}
