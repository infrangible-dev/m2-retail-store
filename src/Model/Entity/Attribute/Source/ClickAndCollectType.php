<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Phrase;

/**
 * The types of the click and collect service of a store.
 */
class ClickAndCollectType extends AbstractSource
{
    /** No click and collect service. */
    public const CLICK_AND_COLLECT_TYPE_NONE = 0;

    /** Click and collect with the stock of the store. */
    public const CLICK_AND_COLLECT_TYPE_STORE_STOCK = 1;

    /** Click and collect without the stock of the store. */
    public const CLICK_AND_COLLECT_TYPE_NO_STORE_STOCK = 2;

    /**
     * Returns the types.
     *
     * @return array
     * @phpstan-return array<int, array{value: int, label: Phrase}>
     */
    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => self::CLICK_AND_COLLECT_TYPE_NONE, 'label' => __('None')],
                [
                    'value' => self::CLICK_AND_COLLECT_TYPE_STORE_STOCK,
                    'label' => __('Click & Collect with store stock')
                ],
                [
                    'value' => self::CLICK_AND_COLLECT_TYPE_NO_STORE_STOCK,
                    'label' => __('Click & Collect with no store stock')
                ]
            ];
        }

        return $this->_options;
    }
}
