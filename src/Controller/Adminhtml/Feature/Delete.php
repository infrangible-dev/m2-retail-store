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
 * Deletes a feature.
 */
class Delete extends \Infrangible\BackendWidget\Controller\Backend\Object\Delete
{
    use Feature;

    /**
     * Returns the message after the deletion.
     *
     * @return string
     */
    protected function getObjectDeletedMessage(): string
    {
        return __('Successfully deleted feature.')->render();
    }
}
