<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ClickAndCollectType
    extends AbstractSource
{
    public const CLICK_AND_COLLECT_TYPE_NONE = 0;
    public const CLICK_AND_COLLECT_TYPE_STORE_STOCK = 1;
    public const CLICK_AND_COLLECT_TYPE_NO_STORE_STOCK = 2;

    public function getAllOptions(): array
    {
        if (is_null($this->_options)) {
            $this->_options = [['value' => static::CLICK_AND_COLLECT_TYPE_NONE,
                'label' => __('None')],
                ['value' => static::CLICK_AND_COLLECT_TYPE_STORE_STOCK,
                    'label' => __('Click & Collect with store stock')],
                ['value' => static::CLICK_AND_COLLECT_TYPE_NO_STORE_STOCK,
                    'label' => __('Click & Collect with no store stock')]];
        }

        return $this->_options;
    }
}
