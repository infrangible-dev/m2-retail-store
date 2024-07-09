<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\ResourceModel\Store;

use Infrangible\RetailStore\Model\Store;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\LocalizedException;

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
        $this->_init(Store::class, \Infrangible\RetailStore\Model\ResourceModel\Store::class);
    }

    /**
     * @throws LocalizedException
     */
    protected function _initSelect(): AbstractCollection
    {
        $this->getSelect()->from(['e' => $this->getEntity()->getEntityTable()]);

        return $this;
    }
}
