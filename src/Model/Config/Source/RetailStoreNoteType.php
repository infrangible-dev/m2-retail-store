<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class RetailStoreNoteType
    implements OptionSourceInterface
{
    /**
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'none', 'label' => __('None')],
            ['value' => 'detail', 'label' => __('Detail')],
            ['value' => 'important', 'label' => __('Important')],
            ['value' => 'notice', 'label' => __('Notice')]
        ];
    }
}
