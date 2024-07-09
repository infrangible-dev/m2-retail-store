<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Controller\Adminhtml\Feature;

use Infrangible\RetailStore\Traits\Feature;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class MassDelete
    extends \Infrangible\BackendWidget\Controller\Backend\Object\MassDelete
{
    use Feature;

    protected function getObjectsDeletedMessage(): string
    {
        return __('Successfully deleted %d features.')->render();
    }
}
