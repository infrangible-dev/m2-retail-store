<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Widget\GoogleMaps;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Registry;
use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\JsonMarker;
use Infrangible\RetailStore\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Stores
    extends JsonMarker
{
    /** @var Data */
    protected $retailStoreHelper;

    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        Json $json,
        Arrays $arrays,
        Variables $variables,
        Data $retailStoreHelper,
        array $data = [])
    {
        parent::__construct($context, $registryHelper, $json, $arrays, $variables, $data);

        $this->retailStoreHelper = $retailStoreHelper;
    }

    public function getMarkerJson(): string
    {
        try {
            return str_replace('<<<', '{', str_replace('>>>', '}',
                $this->retailStoreHelper->getMarkerJson($this->getLayout(),
                    $this->retailStoreHelper->loadActiveRetailStores())));
        } catch (LocalizedException $exception) {
            return '';
        }
    }
}
