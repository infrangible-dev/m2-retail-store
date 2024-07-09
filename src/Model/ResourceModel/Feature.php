<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Feature
    extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('retail_store_feature', 'id');
    }
}
