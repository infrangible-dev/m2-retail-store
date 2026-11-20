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
 * Shows the form of a new retail store.
 */
class Add extends \Infrangible\BackendWidget\Controller\Backend\Object\Add
{
    use Store;
}
