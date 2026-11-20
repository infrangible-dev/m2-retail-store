<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model\ResourceModel\Store;

use Infrangible\RetailStore\Model\ResourceModel\Store as StoreResource;
use Infrangible\RetailStore\Model\Store;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Collection of retail stores.
 */
class Collection extends AbstractCollection
{
    /**
     * Sets the model and the resource model.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(Store::class, StoreResource::class);
    }

    /**
     * Restricts the collection to active stores.
     *
     * @return $this
     * @throws LocalizedException
     */
    public function addActiveFilter(): static
    {
        $this->addAttributeToFilter('status', ['eq' => 1]);

        return $this;
    }
}
