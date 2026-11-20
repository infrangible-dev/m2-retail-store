<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * A feature of retail stores, for example parking or a café, with a name and an image.
 *
 * @method $this setName(string $name)
 * @method string|null getImage()
 * @method $this setImage(string $image)
 * @method string|int|null getStatus()
 * @method $this setStatus(int $status)
 */
class Feature extends AbstractModel
{
    /**
     * Sets the resource model.
     *
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Feature::class);
    }

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string
    {
        $name = $this->getData('name');

        return is_scalar($name) ? (string)$name : '';
    }
}
