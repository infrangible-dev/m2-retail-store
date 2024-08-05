<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Helper;

use Exception;
use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Model\ResourceModel\Store\CollectionFactory;
use Infrangible\RetailStore\Model\Store;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Widget\Model\Template\Filter;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
    extends AbstractHelper
{
    /** @var Arrays */
    protected $arrays;

    /** @var Variables */
    protected $variables;

    /** @var Json */
    protected $json;

    /** @var Cms */
    protected $cmsHelper;

    /** @var CollectionFactory */
    protected $retailStoreCollectionFactory;

    /** @var Config */
    protected $eavConfig;

    /** @var Filter */
    protected $filter;

    /** @var Stores */
    protected $storeHelper;

    /** @var string[] */
    private $attributeCodes;

    public function __construct(
        Arrays $arrays,
        Variables $variables,
        Json $json,
        Cms $cmsHelper,
        Context $context,
        CollectionFactory $retailStoreCollectionFactory,
        Config $eavConfig,
        Filter $filter,
        Stores $storeHelper)
    {
        parent::__construct($context);

        $this->arrays = $arrays;
        $this->variables = $variables;
        $this->json = $json;
        $this->cmsHelper = $cmsHelper;
        $this->retailStoreCollectionFactory = $retailStoreCollectionFactory;
        $this->eavConfig = $eavConfig;
        $this->filter = $filter;
        $this->storeHelper = $storeHelper;
    }


    public function loadRetailStoreWithKey(string $urlKey): ?Store
    {
        $retailStoreCollection = $this->retailStoreCollectionFactory->create();

        $retailStoreCollection->addFieldToFilter('url_key', ['eq' => $urlKey]);
        $retailStoreCollection->addFieldToFilter('status', ['eq' => 1]);

        $retailStoreCollection->load();

        /** @var Store $store */
        $store = $retailStoreCollection->getFirstItem();

        return $store;
    }

    /**
     * @return Store[]
     */
    public function loadActiveRetailStores(): array
    {
        $retailStoreCollection = $this->retailStoreCollectionFactory->create();

        try {
            $retailStoreCollection->addAttributeToSelect('*');
        } catch (LocalizedException $exception) {
        }

        $retailStoreCollection->addFieldToFilter('status', ['eq' => 1]);

        return $retailStoreCollection->getItems();
    }

    public function getRetailStoreData(Store $retailStore): array
    {
        $attributeCodes = $this->getAttributeCodes();

        $retailStoreData = $retailStore->getData();

        foreach ($attributeCodes as $attributeCode) {
            $attributeValue = $retailStore->getDataUsingMethod($attributeCode);

            $retailStoreData[sprintf('has_%s', $attributeCode)] = !$this->variables->isEmpty($attributeValue);
        }

        $retailStoreData['url'] = $retailStore->getUrl();

        $featureData = [];

        foreach ($retailStore->getFeatures() as $feature) {
            $featureData[] = $feature->getData();
        }

        $retailStoreData['features'] = $featureData;

        return $retailStoreData;
    }

    /**
     * @return string[]
     */
    protected function getAttributeCodes(): array
    {
        if ($this->attributeCodes === null) {
            $this->attributeCodes = array_keys($this->eavConfig->getEntityAttributes('retail_store'));
        }

        return $this->attributeCodes;
    }

    /**
     * @param Store[] $retailStores
     *
     * @return array
     */
    public function getRetailStoresData(array $retailStores): array
    {
        $retailStoresData = [];

        foreach ($retailStores as $retailStore) {
            $retailStoresData[] = $this->getRetailStoreData($retailStore);
        }

        return $retailStoresData;
    }

    public function getMarkerJson(array $retailStores): string
    {
        $markerData = [];

        foreach ($retailStores as $retailStore) {
            $infoWindowContent = $this->getInfoWindowContent($retailStore);
            $infoWindowContent = preg_replace('/\t/', '', $infoWindowContent);
            $infoWindowContent = preg_replace('/\r/', '', $infoWindowContent);
            $infoWindowContent = preg_replace('/\n/', '', $infoWindowContent);

            $markerData[] = ['lat' => $retailStore->getLatitude(),
                'lng' => $retailStore->getLongitude(),
                'title' => $retailStore->getName(),
                'info_window' => addslashes(trim($infoWindowContent))];
        }

        return str_replace('{', '<<<', str_replace('}', '>>>', $this->json->encode($markerData)));
    }

    public function getResultBlockOutput(array $retailStores, int $cmsBlockId): ?string
    {
        $cmsBlock = $this->cmsHelper->loadCmsBlock($cmsBlockId);

        $content = $cmsBlock->getId() ? $cmsBlock->getContent() : '';

        $this->filter->setVariables(['retail_stores' => $this->getRetailStoresData($retailStores)]);

        try {
            $markerJson = addslashes(htmlspecialchars($this->getMarkerJson($retailStores)));
        } catch (Exception $exception) {
            $this->_logger->error($exception);

            $markerJson = '';
        }

        $this->filter->setVariables(['marker_json' => $markerJson]);

        try {
            return $this->filter->filter($content);
        } catch (Exception $exception) {
            $this->_logger->error($exception);

            return '';
        }
    }

    public function getInfoWindowContent(Store $retailStore): string
    {
        $cmsBlockId = $this->storeHelper->getStoreConfig('infrangible_retailstore/store/info_window_cms_block_id');

        $cmsBlock = $this->cmsHelper->loadCmsBlock((int)$cmsBlockId);

        $content = $cmsBlock->getContent();

        $this->filter->setVariables($this->getRetailStoreData($retailStore));

        try {
            return $this->filter->filter($content);
        } catch (Exception $exception) {
            $this->_logger->error($exception);

            return '';
        }
    }
}
