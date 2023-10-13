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

class GetNewCustomersToday
{
    /**
     * GetNewCustomersToday constructor.
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
     * @return int
     *
     * @throws Exception
     */
    public function execute(): int
    {
        $connection = $this->resourceConnection->getConnection();

        $today = $this->dateUtil->getDateToday('Y-m-d');
        $tomorrow = $this->dateUtil->addDays(1, $today);

        $query = $connection
            ->select()
            ->from('customer_entity', ['COUNT(*) AS new_customers'])
            ->where('customer_entity.created_at BETWEEN ' . "'$today' AND '$tomorrow'");

        $result = $connection->fetchRow($query);

        return (int) $result['new_customers'] ?? 0;
    }
}
