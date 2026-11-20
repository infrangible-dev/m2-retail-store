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
use Infrangible\RetailStore\Helper\Search;
use Infrangible\RetailStore\Model\Store;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

/**
 * Widget with a map of the active stores within a distance of a center, between a header and a result CMS block and
 * a footer CMS block, which get the stores as variable "retail_stores".
 */
class Radius extends JsonMarker
{
    /** @var Store[]|null */
    private ?array $retailStores = null;

    /**
     * @param Template\Context $context
     * @param Registry $registryHelper
     * @param Json $json
     * @param Arrays $arrays
     * @param Variables $variables
     * @param Data $retailStoreHelper
     * @param Search $retailStoreSearchHelper
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
        protected Search $retailStoreSearchHelper,
        array $data = []
    ) {
        parent::__construct($context, $registryHelper, $json, $arrays, $variables, $data);
    }

    /**
     * Returns the latitude of the center.
     *
     * @return string
     */
    public function getLatitude(): string
    {
        return $this->getStringData('lat');
    }

    /**
     * Returns the longitude of the center.
     *
     * @return string
     */
    public function getLongitude(): string
    {
        return $this->getStringData('lng');
    }

    /**
     * Returns the maximum distance in kilometers.
     *
     * @return string
     */
    public function getMaxDistance(): string
    {
        return $this->getStringData('max_distance');
    }

    /**
     * Returns the ID of the header CMS block.
     *
     * @return int
     */
    public function getHeaderBlockId(): int
    {
        return (int)$this->getStringData('header_block_id');
    }

    /**
     * Returns the ID of the result CMS block.
     *
     * @return int
     */
    public function getResultBlockId(): int
    {
        return (int)$this->getStringData('result_block_id');
    }

    /**
     * Returns the ID of the footer CMS block.
     *
     * @return int
     */
    public function getFooterBlockId(): int
    {
        return (int)$this->getStringData('footer_block_id');
    }

    /**
     * Returns the active stores within the distance, sorted by distance.
     *
     * @return Store[]
     */
    public function getRetailStores(): array
    {
        if ($this->retailStores === null) {
            try {
                $this->retailStores = $this->retailStoreSearchHelper->searchRetailStoresWithCoordinates(
                    (float)$this->getLatitude(),
                    (float)$this->getLongitude(),
                    0,
                    max(0, (int)$this->getMaxDistance())
                );
            } catch (LocalizedException $exception) {
                $this->_logger->error($exception);

                $this->retailStores = [];
            }
        }

        return $this->retailStores;
    }

    /**
     * Returns the markers of the stores as JSON.
     *
     * @return string
     */
    public function getMarkerJson(): string
    {
        return $this->retailStoreHelper->getMarkersJson($this->getRetailStores());
    }

    /**
     * Returns the center of the map: the coordinates of the widget.
     *
     * @return float[]
     */
    public function getMapCenter(): array
    {
        return [(float)$this->getLatitude(), (float)$this->getLongitude()];
    }

    /**
     * Renders the header CMS block, the map, the result CMS block and the footer CMS block.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        $retailStores = $this->getRetailStores();

        return $this->retailStoreHelper->getResultBlockOutput($retailStores, $this->getHeaderBlockId()) .
            parent::_toHtml() .
            $this->retailStoreHelper->getResultBlockOutput($retailStores, $this->getResultBlockId()) .
            $this->retailStoreHelper->getResultBlockOutput($retailStores, $this->getFooterBlockId());
    }
}
