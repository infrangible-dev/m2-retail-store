<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Helper;

use Exception;
use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Setup;
use Infrangible\RetailStore\Block\Widget\GoogleMaps\InfoWindow;
use Infrangible\RetailStore\Model\ResourceModel\Store\CollectionFactory;
use Infrangible\RetailStore\Model\Store;
use Infrangible\RetailStore\Model\Template\Filter;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\SetupInterface;
use Magento\Framework\View\LayoutInterface;

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

    /** @var EavSetupFactory */
    protected $eavSetupFactory;

    /** @var CollectionFactory */
    protected $retailStoreCollectionFactory;

    /** @var Config */
    protected $eavConfig;

    /** @var Filter */
    protected $filter;

    /** @var Setup */
    protected $setupHelper;

    /** @var string[] */
    private $attributeCodes;

    public function __construct(
        Arrays $arrays,
        Variables $variables,
        Json $json,
        Cms $cmsHelper,
        EavSetupFactory $eavSetupFactory,
        Context $context,
        CollectionFactory $retailStoreCollectionFactory,
        Config $eavConfig,
        Filter $filter,
        Setup $setupHelper)
    {
        parent::__construct($context);

        $this->arrays = $arrays;
        $this->variables = $variables;
        $this->json = $json;
        $this->cmsHelper = $cmsHelper;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->retailStoreCollectionFactory = $retailStoreCollectionFactory;
        $this->eavConfig = $eavConfig;
        $this->filter = $filter;
        $this->setupHelper = $setupHelper;
    }

    /**
     * @param SetupInterface $setup
     * @param string $attributeCode
     * @param string $label
     * @param string $type
     * @param string $input
     * @param string|null $default
     * @param int $sortOrder
     * @param bool $userDefined
     * @param bool $required
     * @param bool $visible
     * @param bool $visibleOnFront
     * @param string|null $backendModel
     * @param string|null $sourceModel
     *
     * @throws Exception
     */
    public function addAttribute(
        SetupInterface $setup,
        string $attributeCode,
        string $label,
        string $type,
        string $input,
        ?string $default = null,
        int $sortOrder = 10,
        bool $userDefined = false,
        bool $required = false,
        bool $visible = true,
        bool $visibleOnFront = false,
        string $backendModel = null,
        string $sourceModel = null)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $this->setupHelper->addEavEntityAttribute($eavSetup, 'retail_store', $attributeCode, $label,
            ScopedAttributeInterface::SCOPE_GLOBAL, $type, $input, $sortOrder, $default, $userDefined, $required, false,
            false, false, false, $visible, $visibleOnFront, false, $backendModel, $sourceModel);

        $this->setupHelper->addAttributeToSetAndGroup($eavSetup, 'retail_store', $attributeCode, 'Default', 'Default');
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

    public function getMarkerJson(LayoutInterface $layout, array $retailStores): string
    {
        $markerData = [];

        foreach ($retailStores as $retailStore) {
            $infoWindowContent = $this->getInfoWindowContent($layout, $retailStore);
            $infoWindowContent = preg_replace('/\r/', '', $infoWindowContent);
            $infoWindowContent = preg_replace('/\n/', '', $infoWindowContent);

            $markerData[] = ['lat' => $retailStore->getLatitude(),
                'lng' => $retailStore->getLongitude(),
                'title' => $retailStore->getName(),
                'info_window' => addslashes(trim($infoWindowContent))];
        }

        return str_replace('{', '<<<', str_replace('}', '>>>', $this->json->encode($markerData)));
    }

    public function getResultBlockOutput(LayoutInterface $layout, array $retailStores, int $cmsBlockId): ?string
    {
        $cmsBlock = $this->cmsHelper->loadCmsBlock($cmsBlockId);

        $content = $cmsBlock->getId() ? $cmsBlock->getContent() : '';

        $this->filter->setVariables(['retail_stores' => $this->getRetailStoresData($retailStores)]);

        $this->filter->setVariables(['marker_json' => addslashes(htmlspecialchars($this->getMarkerJson($layout,
            $retailStores)))]);

        try {
            return $this->filter->filter($content);
        } catch (Exception $exception) {
            $this->_logger->error($exception);

            return '';
        }
    }

    public function getInfoWindowContent(LayoutInterface $layout, Store $retailStore): string
    {
        /** @var InfoWindow $infoWindow */
        $infoWindow = $layout->createBlock(InfoWindow::class);

        $infoWindow->setRetailStore($retailStore);

        return $infoWindow->toHtml();
    }
}
