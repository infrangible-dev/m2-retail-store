<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\ResourceModel\Feature;

use Infrangible\RetailStore\Model\Feature;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Collection
    extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(Feature::class, \Infrangible\RetailStore\Model\ResourceModel\Feature::class);
    }
}
