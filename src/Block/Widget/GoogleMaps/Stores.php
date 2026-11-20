<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Block\Widget\GoogleMaps;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\JsonMarker;
use Infrangible\RetailStore\Helper\Data;
use Magento\Framework\View\Element\Template;

/**
 * Widget with a map and all active stores as markers.
 */
class Stores extends JsonMarker
{
    /**
     * @param Template\Context $context
     * @param Registry $registryHelper
     * @param Json $json
     * @param Arrays $arrays
     * @param Variables $variables
     * @param Data $retailStoreHelper
     * @param array $data
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        Json $json,
        Arrays $arrays,
        Variables $variables,
        protected Data $retailStoreHelper,
        array $data = []
    ) {
        parent::__construct($context, $registryHelper, $json, $arrays, $variables, $data);
    }

    /**
     * Returns the markers of all active stores as JSON.
     *
     * @return string
     */
    public function getMarkerJson(): string
    {
        return $this->retailStoreHelper->getMarkersJson($this->retailStoreHelper->loadActiveRetailStores());
    }
}
