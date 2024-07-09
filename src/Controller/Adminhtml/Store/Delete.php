<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Controller\Adminhtml\Store;

use Infrangible\RetailStore\Traits\Store;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Delete
    extends \Infrangible\BackendWidget\Controller\Backend\Object\Delete
{
    use Store;

    protected function getObjectDeletedMessage(): string
    {
        return __('Successfully deleted retail store.')->render();
    }
}
