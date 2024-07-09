<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block;

use Exception;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Registry;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Helper\Data;
use Infrangible\RetailStore\Helper\Search;
use Infrangible\RetailStore\Model\Template\Filter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Store
    extends Template
{
    /** @var Registry */
    protected $registryHelper;

    /** @var Variables */
    protected $variables;

    /** @var Data */
    protected $retailStoreHelper;

    /** @var \Infrangible\RetailStore\Block\Search */
    protected $retailStoreSearchHelper;

    /** @var Stores */
    protected $storeHelper;

    /** @var Filter */
    protected $filter;

    /** @var Cms */
    protected $cmsHelper;

    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        Variables $variables,
        Data $retailStoreHelper,
        Search $retailStoreSearchHelper,
        Stores $storeHelper,
        Filter $filter,
        Cms $cmsHelper,
        array $data = [])
    {
        parent::__construct($context, $data);

        $this->registryHelper = $registryHelper;
        $this->variables = $variables;
        $this->retailStoreHelper = $retailStoreHelper;
        $this->retailStoreSearchHelper = $retailStoreSearchHelper;
        $this->storeHelper = $storeHelper;
        $this->filter = $filter;
        $this->cmsHelper = $cmsHelper;
    }

    public function getContent(): string
    {
        /** @var \Infrangible\RetailStore\Model\Store $retailStore */
        $retailStore = $this->registryHelper->registry('current_retail_store');

        $cmsBlockId = $retailStore->getCmsBlockId();

        if (!$cmsBlockId) {
            $cmsBlockId = $this->storeHelper->getStoreConfig('infrangible_retailstore/store/cms_block_id');
        }

        $cmsBlock = $this->cmsHelper->loadCmsBlock((int)$cmsBlockId);

        $content = $cmsBlock->getContent();

        $this->filter->setVariables($this->retailStoreHelper->getRetailStoreData($retailStore));

        $nearbyStoresData = [];

        try {
            $nearbyStores =
                $this->retailStoreSearchHelper->searchRetailStoresWithCoordinates(floatval($retailStore->getLatitude()),
                    floatval($retailStore->getLongitude()), 4);

            array_shift($nearbyStores);

            $nearbyStoresData = $this->retailStoreHelper->getRetailStoresData($nearbyStores);
        } catch (LocalizedException $exception) {
        }

        $this->filter->setVariables(['nearby_stores' => $nearbyStoresData]);

        try {
            return $this->filter->filter($content);
        } catch (Exception $exception) {
            $this->_logger->error($exception);

            return '';
        }
    }
}
