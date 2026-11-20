<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Test\Unit\Helper;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Helper\Data;
use Infrangible\RetailStore\Model\ResourceModel\Store\CollectionFactory;
use Infrangible\RetailStore\Model\Store;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Helper\Context;
use Magento\Widget\Model\Template\FilterFactory;

/**
 * The helper with fixed markers instead of the markers of stores.
 */
class TestData extends Data
{
    /**
     * @param Context $context
     * @param Variables $variables
     * @param Cms $cmsHelper
     * @param CollectionFactory $retailStoreCollectionFactory
     * @param Config $eavConfig
     * @param FilterFactory $filterFactory
     * @param Stores $storeHelper
     * @param array $markers
     * @phpstan-param array<int, array<string, string|float>> $markers
     */
    public function __construct(
        Context $context,
        Variables $variables,
        Cms $cmsHelper,
        CollectionFactory $retailStoreCollectionFactory,
        Config $eavConfig,
        FilterFactory $filterFactory,
        Stores $storeHelper,
        private readonly array $markers
    ) {
        parent::__construct(
            $context,
            $variables,
            $cmsHelper,
            $retailStoreCollectionFactory,
            $eavConfig,
            $filterFactory,
            $storeHelper
        );
    }

    /**
     * Returns the fixed markers.
     *
     * @param array $retailStores
     * @phpstan-param array<int, Store> $retailStores
     *
     * @return array
     * @phpstan-return array<int, array<string, string|float>>
     */
    public function getMarkers(array $retailStores): array
    {
        return $this->markers;
    }
}
