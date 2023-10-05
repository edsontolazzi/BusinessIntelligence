<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Tolazzi\BusinessIntelligence\Model\Command\GetDateParam;
use Tolazzi\BusinessIntelligence\Model\Command\Product\GetTopSellingProductsQty;
use Tolazzi\BusinessIntelligence\Model\Command\Product\GetTopSellingProductsValue;
use Tolazzi\BusinessIntelligence\Model\Provider\Config;

class BiProductsDataProvider implements ArgumentInterface
{
    /** @var GetDateParam */
    private GetDateParam $getDateParam;
    /** @var GetTopSellingProductsQty */
    private GetTopSellingProductsQty $getTopSellingProductsQty;
    /** @var GetTopSellingProductsValue */
    private GetTopSellingProductsValue $getTopSellingProductsValue;
    /** @var Config */
    private Config $config;

    /**
     * BiProductsDataProvider constructor.
     *
     * @param GetDateParam $getDateParam
     * @param GetTopSellingProductsQty $getTopSellingProductsQty
     * @param GetTopSellingProductsValue $getTopSellingProductsValue
     * @param Config $config
     */
    public function __construct(
        GetDateParam $getDateParam,
        GetTopSellingProductsQty $getTopSellingProductsQty,
        GetTopSellingProductsValue $getTopSellingProductsValue,
        Config $config
    ) {
        $this->config = $config;
        $this->getTopSellingProductsValue = $getTopSellingProductsValue;
        $this->getTopSellingProductsQty = $getTopSellingProductsQty;
        $this->getDateParam = $getDateParam;
    }

    /**
     * GetData method
     *
     * @return array
     */
    public function getData(): array
    {
        if (!$this->isModuleEnabled() || !$this->isProductKPIEnabled()) {
            return [];
        }

        $dateParams = $this->getDateParam->execute();
        $from = $dateParams['from'];
        $to = $dateParams['to'];

        return [
            'top_selling_products_qty' => $this->getTopSellingProductsQty->execute($from, $to),
            'top_selling_products_value' => $this->getTopSellingProductsValue->execute($from, $to),
        ];
    }

    /**
     * IsModuleEnabled method
     *
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->config->isModuleEnabled();
    }

    /**
     * IsProductKPIEnabled method
     *
     * @return bool
     */
    public function isProductKPIEnabled(): bool
    {
        return $this->config->isProductKPIEnabled();
    }

    /**
     * GetKPIConfigs method
     *
     * @return array
     */
    public function getKPIConfigs(): array
    {
        return $this->config->getProductActiveKPIs();
    }
}
