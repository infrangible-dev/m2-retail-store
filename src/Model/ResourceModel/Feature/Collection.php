<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model\ResourceModel\Feature;

use Infrangible\RetailStore\Model\Feature;
use Infrangible\RetailStore\Model\ResourceModel\Feature as FeatureResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection of features.
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
        $this->_init(Feature::class, FeatureResource::class);
    }
}
