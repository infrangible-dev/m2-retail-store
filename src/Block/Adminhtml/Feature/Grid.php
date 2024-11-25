<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Adminhtml\Feature;

use Exception;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid
    extends \Infrangible\BackendWidget\Block\Grid
{
    protected function prepareCollection(AbstractDb $collection): void
    {
    }

    /**
     * @throws Exception
     */
    protected function prepareFields(): void
    {
        $this->addTextColumn('name', __('Name')->render());
        $this->addTextColumn('image', __('Image')->render());
        $this->addYesNoColumn('status', __('Active')->render());
    }

    /**
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return [];
    }
}
