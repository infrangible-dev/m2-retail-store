<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Controller\Adminhtml\Store;

use Infrangible\RetailStore\Traits\Store;

/**
 * Returns the grid of the retail stores.
 */
class Grid extends \Infrangible\BackendWidget\Controller\Backend\Object\Grid
{
    use Store;
}
