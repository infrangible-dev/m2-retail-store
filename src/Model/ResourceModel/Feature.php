<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model of the features in the table "retail_store_feature".
 */
class Feature extends AbstractDb
{
    /**
     * Sets the table and the ID field.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('retail_store_feature', 'id');
    }
}
