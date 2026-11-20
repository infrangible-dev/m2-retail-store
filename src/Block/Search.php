<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Block;

use Infrangible\Core\Helper\Registry;
use Infrangible\RetailStore\Controller\Store\Search as SearchAction;
use Infrangible\RetailStore\Helper\Data;
use Infrangible\RetailStore\Model\Store;
use Magento\Framework\View\Element\Template;

/**
 * The result of the store search: the result CMS block with the found stores, or the CMS block for no result.
 */
class Search extends Template
{
    /**
     * @param Template\Context $context
     * @param Registry $registryHelper
     * @param Data $retailStoreHelper
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(
        Template\Context $context,
        protected Registry $registryHelper,
        protected Data $retailStoreHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Returns the found stores.
     *
     * @return Store[]
     */
    public function getRetailStores(): array
    {
        $retailStores = $this->registryHelper->registry(SearchAction::REGISTRY_KEY_RETAIL_STORES);

        return is_array($retailStores) ?
            array_values(array_filter($retailStores, static fn($retailStore): bool => $retailStore instanceof Store)) :
            [];
    }

    /**
     * Returns the content of the result CMS block with the found stores, or of the CMS block for no result.
     *
     * @return string
     */
    public function getContent(): string
    {
        $retailStores = $this->getRetailStores();

        $cmsBlockId = $this->registryHelper->registry(
            count($retailStores) > 0 ?
                SearchAction::REGISTRY_KEY_RESULT_BLOCK_ID : SearchAction::REGISTRY_KEY_NO_RESULT_BLOCK_ID
        );

        return is_int($cmsBlockId) ? $this->retailStoreHelper->getResultBlockOutput($retailStores, $cmsBlockId) : '';
    }
}
