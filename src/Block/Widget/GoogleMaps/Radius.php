<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Widget\GoogleMaps;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\JsonMarker;
use Infrangible\RetailStore\Helper\Data;
use Infrangible\RetailStore\Model\Store;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Radius
    extends JsonMarker
{
    /** @var Data */
    protected $retailStoreHelper;

    /** @var \Infrangible\RetailStore\Helper\Search */
    protected $retailStoreSearchHelper;

    /** @var Store[] */
    private $retailStores;

    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        Json $json,
        Arrays $arrays,
        Variables $variables,
        Data $retailStoreHelper,
        \Infrangible\RetailStore\Helper\Search $retailStoreSearchHelper,
        array $data = [])
    {
        parent::__construct($context, $registryHelper, $json, $arrays, $variables, $data);

        $this->retailStoreHelper = $retailStoreHelper;
        $this->retailStoreSearchHelper = $retailStoreSearchHelper;
    }

    public function getLatitude(): string
    {
        return $this->getData('lat');
    }

    public function getLongitude(): string
    {
        return $this->getData('lng');
    }

    public function getMaxDistance(): string
    {
        return $this->getData('max_distance');
    }

    public function getHeaderBlockId(): int
    {
        return (int)$this->getData('header_block_id');
    }

    public function getResultBlockId(): int
    {
        return (int)$this->getData('result_block_id');
    }

    public function getFooterBlockId(): int
    {
        return (int)$this->getData('footer_block_id');
    }

    /**
     * @return Store[]
     */
    public function getRetailStores(): array
    {
        $latitude = (float)$this->getLatitude();
        $longitude = (float)$this->getLongitude();
        $maxDistance = (int)$this->getMaxDistance();

        try {
            if ($this->retailStores === null) {
                $this->retailStores =
                    $this->retailStoreSearchHelper->searchRetailStoresWithCoordinates($latitude, $longitude, 0,
                        $maxDistance);
            }
        } catch (LocalizedException $exception) {
            $this->retailStores = [];
        }

        return $this->retailStores;
    }

    public function getMarkerJson(): string
    {
        try {
            return str_replace('<<<', '{',
                str_replace('>>>', '}', $this->retailStoreHelper->getMarkerJson($this->getRetailStores())));
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
            return '';
        }
    }

    /**
     * @return float[]
     */
    public function getMapCenter(): array
    {
        $latitude = (float)$this->getLatitude();
        $longitude = (float)$this->getLongitude();

        return [$latitude, $longitude];
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        try {
            $html = $this->retailStoreHelper->getResultBlockOutput($this->getRetailStores(), $this->getHeaderBlockId());

            $html .= parent::_toHtml();

            $html .= $this->retailStoreHelper->getResultBlockOutput($this->getRetailStores(),
                $this->getResultBlockId());

            $html .= $this->retailStoreHelper->getResultBlockOutput($this->getRetailStores(),
                $this->getFooterBlockId());
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
            $html = '';
        }

        return $html;
    }
}
