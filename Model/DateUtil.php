<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class DateUtil
{
    /** @var TimezoneInterface */
    private TimezoneInterface $timezone;
    /** @var StoreManagerInterface */
    private StoreManagerInterface $storeManager;

    /**
     * DateUtil constructor.
     *
     * @param TimezoneInterface $timezone
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        TimezoneInterface $timezone,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
    }

    /**
     * AddDays method
     *
     * @param int $days
     * @param string $date
     *
     * @return string
     */
    public function addDays(int $days, string $date): string
    {
        return date('Y-m-d', strtotime("+$days days", strtotime($date)));
    }

    /**
     * GetDateToday method
     *
     * @param string $format
     *
     * @return string
     * @throws Exception
     */
    public function getDateToday(string $format): string
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $storeId = null;
        }
        $timezone = $this->timezone->getConfigTimezone(ScopeInterface::SCOPE_STORES, $storeId);
        $currentDate = new \DateTime('now', new \DateTimeZone($timezone));

        return $currentDate->format($format);
    }
}
