<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const BASE_PATH = 'business_intelligence';
    public const GENERAL_GROUP = 'general';
    public const GENERAL_SECTION_GROUP = 'general_section';
    public const PRODUCT_SECTION_GROUP = 'product_section';
    public const SALES_SECTION_GROUP = 'sales_section';
    public const CUSTOMER_SECTION_GROUP = 'customer_section';
    public const ENABLED_FIELD = 'enabled';
    public const ABANDONED_CART_TIME_FIELD = 'abandoned_cart_time';
    public const LAST_INFORMATIONS_FIELD = 'last_informations';
    public const TOP_INFORMATIONS_FIELD = 'top_informations';
    public const KPI_FIELD = 'kpi';

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * GetValue method
     *
     * @param string $field
     * @param mixed|null $storeId
     *
     * @return mixed
     */
    protected function getValue(string $field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            static::BASE_PATH . '/' . $field,
            ScopeInterface::SCOPE_WEBSITES,
            $storeId
        );
    }

    /**
     * IsModuleEnabled method
     *
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return (bool) $this->getValue(self::GENERAL_GROUP . '/' . self::ENABLED_FIELD);
    }

    /**
     * IsGeneralKPIEnabled method
     *
     * @return bool
     */
    public function isGeneralKPIEnabled(): bool
    {
        return (bool) $this->getValue(self::GENERAL_SECTION_GROUP . '/' . self::ENABLED_FIELD);
    }

    /**
     * IsProductKPIEnabled method
     *
     * @return bool
     */
    public function isProductKPIEnabled(): bool
    {
        return (bool) $this->getValue(self::PRODUCT_SECTION_GROUP . '/' . self::ENABLED_FIELD);
    }

    /**
     * IsSalesKPIEnabled method
     *
     * @return bool
     */
    public function isSalesKPIEnabled(): bool
    {
        return (bool) $this->getValue(self::SALES_SECTION_GROUP . '/' . self::ENABLED_FIELD);
    }

    /**
     * IsCustomerKPIEnabled method
     *
     * @return bool
     */
    public function isCustomerKPIEnabled(): bool
    {
        return (bool) $this->getValue(self::CUSTOMER_SECTION_GROUP . '/' . self::ENABLED_FIELD);
    }

    /**
     * GetGeneralTimeToAbandonedCart method
     *
     * @return int
     */
    public function getGeneralTimeToAbandonedCart(): int
    {
        return (int) $this->getValue(self::GENERAL_SECTION_GROUP . '/' . self::ABANDONED_CART_TIME_FIELD);
    }

    /**
     * GetGeneralLastInformationRowTableNumber method
     *
     * @return int
     */
    public function getGeneralLastInformationRowTableNumber(): int
    {
        return (int) $this->getValue(self::GENERAL_SECTION_GROUP . '/' . self::LAST_INFORMATIONS_FIELD);
    }

    /**
     * GetGeneralTopInformationRowTableNumber method
     *
     * @return int
     */
    public function getGeneralTopInformationRowTableNumber(): int
    {
        return (int) $this->getValue(self::GENERAL_SECTION_GROUP . '/' . self::TOP_INFORMATIONS_FIELD);
    }

    /**
     * GetProductTopInformationRowTableNumber method
     *
     * @return int
     */
    public function getProductTopInformationRowTableNumber(): int
    {
        return (int) $this->getValue(self::PRODUCT_SECTION_GROUP . '/' . self::TOP_INFORMATIONS_FIELD);
    }

    /**
     * GetGeneralActiveKPIs method
     *
     * @return array
     */
    public function getGeneralActiveKPIs(): array
    {
        $value = $this->getValue(self::GENERAL_SECTION_GROUP . '/' . self::KPI_FIELD);

        return explode(',', $value ?? '');
    }

    /**
     * GetProductActiveKPIs method
     *
     * @return array
     */
    public function getProductActiveKPIs(): array
    {
        $value = $this->getValue(self::PRODUCT_SECTION_GROUP . '/' . self::KPI_FIELD);

        return explode(',', $value ?? '');
    }

    /**
     * GetSalesActiveKPIs method
     *
     * @return array
     */
    public function getSalesActiveKPIs(): array
    {
        $value = $this->getValue(self::SALES_SECTION_GROUP . '/' . self::KPI_FIELD);

        return explode(',', $value ?? '');
    }

    /**
     * GetCustomerActiveKPIs method
     *
     * @return array
     */
    public function getCustomerActiveKPIs(): array
    {
        $value = $this->getValue(self::CUSTOMER_SECTION_GROUP . '/' . self::KPI_FIELD);

        return explode(',', $value ?? '');
    }
}
