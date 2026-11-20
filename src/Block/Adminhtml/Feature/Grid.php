<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Block\Adminhtml\Feature;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Grid of the features.
 */
class Grid extends \Infrangible\BackendWidget\Block\Grid
{
    /**
     * The grid shows all features.
     *
     * @param AbstractDb $collection
     * @phpstan-param AbstractDb<DataObject> $collection
     *
     * @return void
     */
    // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock -- the grid shows all features
    protected function prepareCollection(AbstractDb $collection): void
    {
    }

    /**
     * Adds the columns.
     *
     * @return void
     * @throws Exception
     */
    protected function prepareFields(): void
    {
        $this->addTextColumn('name', __('Name')->render());
        $this->addTextColumn('image', __('Image')->render());
        $this->addYesNoColumn('status', __('Active')->render());
    }

    /**
     * The grid has no hidden fields.
     *
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return [];
    }
}
