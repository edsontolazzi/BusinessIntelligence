<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command\General;

use Magento\Framework\App\ResourceConnection;

class GetMostPopularitySearchTerms
{
    public const QUERY_TEXT_COLUMN = 'query_text';
    public const POPULARITY = 'popularity';

    /** @var ResourceConnection */
    private ResourceConnection $resourceConnection;

    /**
     * GetMostPopularitySearchTerms constructor.
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
     * @return array
     */
    public function execute(): array
    {
        $connection = $this->resourceConnection->getConnection();

        $query = $connection
            ->select()
            ->from('search_query', ['query_text', 'popularity'])
            ->order('popularity DESC')
            ->limit(5);

        $result = $connection->fetchAll($query);

        return $result ?? [];
    }
}
