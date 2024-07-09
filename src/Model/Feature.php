<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Model;

use Magento\Framework\Model\AbstractModel;
use Infrangible\RetailStore\Model\ResourceModel;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 *
 * @method string getName()
 * @method void setName(string $name)
 * @method string getImage()
 * @method void setImage(string $image)
 * @method int getStatus()
 * @method void setStatus(int $status)
 */
class Feature
    extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Feature::class);
    }
}
