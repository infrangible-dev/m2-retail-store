<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Controller\Adminhtml\Feature;

use Infrangible\RetailStore\Traits\Feature;

/**
 * Shows the form of a new feature.
 */
class Add extends \Infrangible\BackendWidget\Controller\Backend\Object\Add
{
    use Feature;
}
