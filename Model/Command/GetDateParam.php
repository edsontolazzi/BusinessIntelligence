<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Command;

use Magento\Framework\App\RequestInterface;

class GetDateParam
{
    /**
     * GetDateParam constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly RequestInterface $request
    ) {
    }

    /**
     * Execute method
     *
     * @return array
     */
    public function execute(): array
    {
        $from = $this->request->getParam('from') ?? date('Y-m-d', strtotime(date('Y-m-1')));
        $to = $this->request->getParam('to') ?? date('Y-m-d');

        return [
            'from' => $from,
            'to' => $to,
        ];
    }
}