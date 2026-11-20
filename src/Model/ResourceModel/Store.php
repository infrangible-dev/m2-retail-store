<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model\ResourceModel;

use Infrangible\RetailStore\Model\Store as StoreModel;
use Magento\Eav\Model\Entity\AbstractEntity;

/**
 * Resource model of the retail stores, an EAV entity with the table "retail_store_entity".
 */
class Store extends AbstractEntity
{
    /**
     * Sets the entity type.
     *
     * @return void
     */
    protected function _construct(): void
    {
        parent::_construct();

        $this->setType(StoreModel::ENTITY);
    }
}
