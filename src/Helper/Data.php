<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Helper;

use Exception;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Model\ResourceModel\Store\CollectionFactory;
use Infrangible\RetailStore\Model\Store;
use Magento\Cms\Model\Block;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Widget\Model\Template\FilterFactory;

/**
 * Loads retail stores and renders their data with the templates of CMS blocks.
 */
class Data extends AbstractHelper
{
    /** Configuration path of the CMS block for the info windows of the markers. */
    public const CONFIG_INFO_WINDOW_CMS_BLOCK_ID = 'infrangible_retailstore/store/info_window_cms_block_id';

    /** Configuration path of the default CMS block of the store pages. */
    public const CONFIG_CMS_BLOCK_ID = 'infrangible_retailstore/store/cms_block_id';

    /** @var string[]|null */
    private ?array $attributeCodes = null;

    /** @var array<int, Block|null> */
    private array $cmsBlocks = [];

    /**
     * @param Context $context
     * @param Variables $variables
     * @param Cms $cmsHelper
     * @param CollectionFactory $retailStoreCollectionFactory
     * @param Config $eavConfig
     * @param FilterFactory $filterFactory
     * @param Stores $storeHelper
     */
    public function __construct(
        Context $context,
        protected Variables $variables,
        protected Cms $cmsHelper,
        protected CollectionFactory $retailStoreCollectionFactory,
        protected Config $eavConfig,
        protected FilterFactory $filterFactory,
        protected Stores $storeHelper
    ) {
        parent::__construct($context);
    }

    /**
     * Returns the active store with the URL key, or null if there is none.
     *
     * @param string $urlKey
     *
     * @return Store|null
     * @throws LocalizedException
     */
    public function loadRetailStoreWithKey(string $urlKey): ?Store
    {
        if ($urlKey === '') {
            return null;
        }

        $retailStoreCollection = $this->retailStoreCollectionFactory->create();

        $retailStoreCollection->addAttributeToSelect('*');
        $retailStoreCollection->addAttributeToFilter('url_key', ['eq' => $urlKey]);
        $retailStoreCollection->addActiveFilter();
        $retailStoreCollection->setPageSize(1);

        $retailStore = $retailStoreCollection->getFirstItem();

        return $retailStore instanceof Store && $retailStore->getId() ? $retailStore : null;
    }

    /**
     * Returns all active stores with all attributes.
     *
     * @return Store[]
     */
    public function loadActiveRetailStores(): array
    {
        try {
            $retailStoreCollection = $this->retailStoreCollectionFactory->create();

            $retailStoreCollection->addAttributeToSelect('*');
            $retailStoreCollection->addActiveFilter();

            return array_values(
                array_filter(
                    $retailStoreCollection->getItems(),
                    static fn($retailStore): bool => $retailStore instanceof Store
                )
            );
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception);

            return [];
        }
    }

    /**
     * Returns the data of the store for templates.
     *
     * The data are its attributes, "has_<attribute>" for every attribute, its URL and the data of its features.
     *
     * @param Store $retailStore
     *
     * @return array
     * @phpstan-return array<string, mixed>
     */
    public function getRetailStoreData(Store $retailStore): array
    {
        /** @var array<string, mixed> $retailStoreData */
        $retailStoreData = $retailStore->getData();

        foreach ($this->getAttributeCodes() as $attributeCode) {
            $retailStoreData[ sprintf('has_%s', $attributeCode) ] =
                ! $this->variables->isEmpty($retailStore->getData($attributeCode));
        }

        $retailStoreData[ 'url' ] = $retailStore->getUrl();

        $featureData = [];

        foreach ($retailStore->getFeatures() as $feature) {
            $featureData[] = $feature->getData();
        }

        $retailStoreData[ 'features' ] = $featureData;

        return $retailStoreData;
    }

    /**
     * Returns the codes of the attributes of the stores.
     *
     * @return string[]
     */
    protected function getAttributeCodes(): array
    {
        $this->attributeCodes ??= array_map(
            'strval',
            array_keys($this->eavConfig->getEntityAttributes(Store::ENTITY))
        );

        return $this->attributeCodes;
    }

    /**
     * Returns the data of the stores for templates.
     *
     * @param Store[] $retailStores
     *
     * @return array
     * @phpstan-return array<int, array<string, mixed>>
     */
    public function getRetailStoresData(array $retailStores): array
    {
        $retailStoresData = [];

        foreach ($retailStores as $retailStore) {
            $retailStoresData[] = $this->getRetailStoreData($retailStore);
        }

        return $retailStoresData;
    }

    /**
     * Returns the markers of the stores with coordinates.
     *
     * Every marker has the latitude (lat), longitude (lng), title and the content of the info window (info_window).
     *
     * @param Store[] $retailStores
     *
     * @return array
     * @phpstan-return array<int, array<string, string|float>>
     */
    public function getMarkers(array $retailStores): array
    {
        $markers = [];

        foreach ($retailStores as $retailStore) {
            $latitude = $retailStore->getLatitude();
            $longitude = $retailStore->getLongitude();

            if ($latitude === null || $longitude === null) {
                continue;
            }

            $markers[] = [
                'lat'         => $latitude,
                'lng'         => $longitude,
                'title'       => $retailStore->getName(),
                'info_window' => trim(
                    (string)preg_replace('/[\t\r\n]/', '', $this->getInfoWindowContent($retailStore))
                )
            ];
        }

        return $markers;
    }

    /**
     * Returns the markers of the stores as JSON.
     *
     * The JSON contains no quotes, ampersands or angle brackets inside its texts, so that it can be part of HTML.
     *
     * @param Store[] $retailStores
     *
     * @return string
     */
    public function getMarkersJson(array $retailStores): string
    {
        $json = json_encode(
            $this->getMarkers($retailStores),
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_PRESERVE_ZERO_FRACTION
        );

        return $json === false ? '[]' : $json;
    }

    /**
     * Returns the markers of the stores as value for the parameter "marker_json" of the widget of Google Maps.
     *
     * Curly braces would end the widget directive, so they are written as <<< and >>> (and as unicode escape sequences
     * inside texts). Quotes would end the parameter, so they are written as entities. Magento removes one of two
     * backslashes in parameters, so they are doubled.
     *
     * @param Store[] $retailStores
     *
     * @return string
     */
    public function getMarkerJson(array $retailStores): string
    {
        $json = (string)preg_replace_callback(
            '/"(?:[^"\\\\]|\\\\.)*"/',
            static fn(array $matches): string => str_replace(['{', '}'], ['{', '}'], $matches[ 0 ]),
            $this->getMarkersJson($retailStores)
        );

        return str_replace(
            ['\\', '"', '{', '}'],
            ['\\\\', '&quot;', '<<<', '>>>'],
            $json
        );
    }

    /**
     * Returns the content of the CMS block, filtered with the stores and their markers.
     *
     * The stores are the variable "retail_stores", their markers the variable "marker_json" (see getMarkerJson()). The
     * block must be active and assigned to the current store view, otherwise the result is empty.
     *
     * @param Store[] $retailStores
     * @param int $cmsBlockId
     *
     * @return string
     */
    public function getResultBlockOutput(array $retailStores, int $cmsBlockId): string
    {
        $cmsBlock = $this->getCmsBlock($cmsBlockId);

        if ($cmsBlock === null) {
            return '';
        }

        return $this->filterContent(
            $this->getCmsBlockContent($cmsBlock),
            [
                'retail_stores' => $this->getRetailStoresData($retailStores),
                'marker_json'   => $this->getMarkerJson($retailStores)
            ]
        );
    }

    /**
     * Returns the content of the info window of the store.
     *
     * It is the CMS block of the configuration, filtered with the data of the store.
     *
     * @param Store $retailStore
     *
     * @return string
     */
    public function getInfoWindowContent(Store $retailStore): string
    {
        $cmsBlock = $this->getCmsBlock($this->getConfigCmsBlockId(self::CONFIG_INFO_WINDOW_CMS_BLOCK_ID));

        if ($cmsBlock === null) {
            return '';
        }

        return $this->filterContent($this->getCmsBlockContent($cmsBlock), $this->getRetailStoreData($retailStore));
    }

    /**
     * Returns the ID of the CMS block of the store page.
     *
     * It is the block of the store, or the block of the configuration.
     *
     * @param Store $retailStore
     *
     * @return int
     */
    public function getStoreCmsBlockId(Store $retailStore): int
    {
        $cmsBlockId = $retailStore->getCmsBlockId();

        return $cmsBlockId > 0 ? $cmsBlockId : $this->getConfigCmsBlockId(self::CONFIG_CMS_BLOCK_ID);
    }

    /**
     * Returns the ID of a CMS block in the configuration.
     *
     * @param string $path
     *
     * @return int
     */
    protected function getConfigCmsBlockId(string $path): int
    {
        $cmsBlockId = $this->storeHelper->getStoreConfig($path);

        return is_numeric($cmsBlockId) ? (int)$cmsBlockId : 0;
    }

    /**
     * Returns the CMS block if it is active and assigned to the current store view, otherwise null.
     *
     * @param int $cmsBlockId
     *
     * @return Block|null
     */
    public function getCmsBlock(int $cmsBlockId): ?Block
    {
        if ($cmsBlockId <= 0) {
            return null;
        }

        if (! array_key_exists($cmsBlockId, $this->cmsBlocks)) {
            $cmsBlock = $this->cmsHelper->loadCmsBlock($cmsBlockId);

            $this->cmsBlocks[ $cmsBlockId ] = $cmsBlock->getId() && $cmsBlock->isActive() &&
                $this->isAssignedToCurrentStore($cmsBlock) ? $cmsBlock : null;
        }

        return $this->cmsBlocks[ $cmsBlockId ];
    }

    /**
     * Returns the content of the CMS block, or an empty text; the database allows blocks without content.
     *
     * @param Block $cmsBlock
     *
     * @return string
     */
    public function getCmsBlockContent(Block $cmsBlock): string
    {
        return $this->variables->stringValueOrDefault($cmsBlock->getData(Block::CONTENT));
    }

    /**
     * Checks if the CMS block is assigned to all store views or the current one.
     *
     * @param Block $cmsBlock
     *
     * @return bool
     */
    protected function isAssignedToCurrentStore(Block $cmsBlock): bool
    {
        $storeIds = $cmsBlock->getStores();

        if (! is_array($storeIds)) {
            return true;
        }

        $storeIds = array_map('intval', $storeIds);

        return in_array(0, $storeIds, true) || in_array($this->storeHelper->getStoreId(), $storeIds, true);
    }

    /**
     * Filters the content with a new widget filter of the current store view and the variables.
     *
     * A new filter is used, because the variables of a filter are kept until it is destroyed.
     *
     * @param string $content
     * @param array $variables
     * @phpstan-param array<string, mixed> $variables
     *
     * @return string
     */
    public function filterContent(string $content, array $variables): string
    {
        if ($content === '') {
            return '';
        }

        try {
            $filter = $this->filterFactory->create();

            $filter->setStoreId($this->storeHelper->getStoreId());
            $filter->setVariables($variables);

            return $filter->filter($content);
        } catch (Exception $exception) {
            $this->_logger->error($exception);

            return '';
        }
    }
}
