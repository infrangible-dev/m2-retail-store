<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Block;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\RetailStore\Controller\Store\View;
use Infrangible\RetailStore\Helper\Data;
use Infrangible\RetailStore\Helper\Search;
use Infrangible\RetailStore\Model\Store as StoreModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

/**
 * The page of a store: the CMS block of the store, filtered with its data and the nearby stores (variable
 * "nearby_stores").
 */
class Store extends Template
{
    /** Number of nearby stores. */
    public const NEARBY_STORES = 3;

    /**
     * @param Template\Context $context
     * @param Registry $registryHelper
     * @param Variables $variables
     * @param Data $retailStoreHelper
     * @param Search $retailStoreSearchHelper
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(
        Template\Context $context,
        protected Registry $registryHelper,
        protected Variables $variables,
        protected Data $retailStoreHelper,
        protected Search $retailStoreSearchHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Returns the current store.
     *
     * @return StoreModel|null
     */
    public function getRetailStore(): ?StoreModel
    {
        $retailStore = $this->registryHelper->registry(View::REGISTRY_KEY);

        return $retailStore instanceof StoreModel ? $retailStore : null;
    }

    /**
     * Returns the content of the CMS block of the store.
     *
     * @return string
     */
    public function getContent(): string
    {
        $retailStore = $this->getRetailStore();

        if ($retailStore === null) {
            return '';
        }

        $cmsBlock = $this->retailStoreHelper->getCmsBlock($this->retailStoreHelper->getStoreCmsBlockId($retailStore));

        if ($cmsBlock === null) {
            return '';
        }

        $variables = $this->retailStoreHelper->getRetailStoreData($retailStore);

        $variables[ 'nearby_stores' ] = $this->retailStoreHelper->getRetailStoresData(
            $this->getNearbyStores($retailStore)
        );

        return $this->retailStoreHelper->filterContent(
            $this->retailStoreHelper->getCmsBlockContent($cmsBlock),
            $variables
        );
    }

    /**
     * Returns the stores nearest to the store.
     *
     * @param StoreModel $retailStore
     *
     * @return StoreModel[]
     */
    public function getNearbyStores(StoreModel $retailStore): array
    {
        $latitude = $retailStore->getLatitude();
        $longitude = $retailStore->getLongitude();

        if ($latitude === null || $longitude === null) {
            return [];
        }

        try {
            return $this->retailStoreSearchHelper->searchRetailStoresWithCoordinates(
                $latitude,
                $longitude,
                self::NEARBY_STORES,
                0,
                [$this->variables->intValue($retailStore->getId())]
            );
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception);

            return [];
        }
    }
}
