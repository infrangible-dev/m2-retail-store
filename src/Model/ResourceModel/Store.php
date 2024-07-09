<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Store
    extends AbstractEntity
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setType(\Infrangible\RetailStore\Model\Store::ENTITY);
    }
}
