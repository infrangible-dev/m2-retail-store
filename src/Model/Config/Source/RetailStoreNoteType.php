<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Phrase;

/**
 * The types of the note of a store, which templates can use for example as CSS class.
 */
class RetailStoreNoteType implements OptionSourceInterface
{
    /** Note without type. */
    public const NONE = 'none';

    /** Note with details. */
    public const DETAIL = 'detail';

    /** Important note. */
    public const IMPORTANT = 'important';

    /** Notice. */
    public const NOTICE = 'notice';

    /**
     * Returns the types.
     *
     * @return array
     * @phpstan-return array<int, array{value: string, label: Phrase}>
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::NONE, 'label' => __('None')],
            ['value' => self::DETAIL, 'label' => __('Detail')],
            ['value' => self::IMPORTANT, 'label' => __('Important')],
            ['value' => self::NOTICE, 'label' => __('Notice')]
        ];
    }
}
