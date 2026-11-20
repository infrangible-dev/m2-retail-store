<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Block\Adminhtml\Store;

use Exception;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;

/**
 * Grid of the retail stores.
 */
class Grid extends \Infrangible\BackendWidget\Block\Grid
{
    /**
     * Adds the attributes of the columns to the collection.
     *
     * @param AbstractDb $collection
     * @phpstan-param AbstractDb<DataObject> $collection
     *
     * @return void
     * @throws LocalizedException
     */
    protected function prepareCollection(AbstractDb $collection): void
    {
        if ($collection instanceof AbstractCollection) {
            $collection->addAttributeToSelect(
                ['url_key', 'code', 'name', 'street', 'postcode', 'city', 'country', 'status']
            );
        }
    }

    /**
     * Adds the columns.
     *
     * @return void
     * @throws Exception
     */
    protected function prepareFields(): void
    {
        $this->addTextColumn('url_key', __('URL Key')->render());
        $this->addTextColumn('code', __('Code')->render());
        $this->addTextColumn('name', __('Name')->render());
        $this->addTextColumn('street', __('Street')->render());
        $this->addTextColumn('postcode', __('Postal Code')->render());
        $this->addTextColumn('city', __('City')->render());
        $this->addCountryColumn('country', __('Country')->render());
        $this->addYesNoColumn('status', __('Active')->render());
    }

    /**
     * The grid has no hidden fields.
     *
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return [];
    }
}
