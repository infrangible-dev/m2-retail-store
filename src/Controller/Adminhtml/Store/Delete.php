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
 * Deletes a retail store.
 */
class Delete extends \Infrangible\BackendWidget\Controller\Backend\Object\Delete
{
    use Store;

    /**
     * Returns the message after the deletion.
     *
     * @return string
     */
    protected function getObjectDeletedMessage(): string
    {
        return __('Successfully deleted retail store.')->render();
    }
}
