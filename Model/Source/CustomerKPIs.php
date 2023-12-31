<?php
/**
 * @author      Edson Tolazzi <edson.tolazzi@universo.univates.br>
 * @copyright   2023 Edson Tolazzi
 */

declare(strict_types=1);

namespace Tolazzi\BusinessIntelligence\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CustomerKPIs implements OptionSourceInterface
{
    /**
     * CustomerKPIs constructor.
     *
     * @param array $options
     */
    public function __construct(
        private readonly array $options
    ) {
    }

    /**
     * ToOptionArray method
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];

        foreach ($this->options as $key => $option) {
            $options[] = [
                'value' => $key,
                'label' => __($option),
            ];
        }

        return $options;
    }
}
