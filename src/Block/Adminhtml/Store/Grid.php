<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Adminhtml\Store;

use Exception;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Grid
    extends \Infrangible\BackendWidget\Block\Grid
{
    /**
     * @param AbstractDb $collection
     *
     * @throws LocalizedException
     */
    protected function prepareCollection(AbstractDb $collection)
    {
        if ($collection instanceof AbstractCollection) {
            $collection->addAttributeToSelect('url_key');
            $collection->addAttributeToSelect('code');
            $collection->addAttributeToSelect('name');
            $collection->addAttributeToSelect('street');
            $collection->addAttributeToSelect('postcode');
            $collection->addAttributeToSelect('city');
            $collection->addAttributeToSelect('country');
            $collection->addAttributeToSelect('status');
        }
    }

    /**
     * @throws Exception
     */
    protected function prepareFields()
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
     * @return string[]
     */
    protected function getHiddenFieldNames(): array
    {
        return [];
    }
}
