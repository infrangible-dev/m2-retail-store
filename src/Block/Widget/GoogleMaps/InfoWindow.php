<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Widget\GoogleMaps;

use Infrangible\RetailStore\Model\Store;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InfoWindow
    extends Template
{
    /** @var Store */
    private $retailStore;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('Infrangible_RetailStore::info_window.phtml');

        parent::_construct();
    }

    public function getRetailStore(): Store
    {
        return $this->retailStore;
    }

    public function setRetailStore(Store $retailStore): void
    {
        $this->retailStore = $retailStore;
    }

    public function prepareData(?string $data): ?string
    {
        return trim(preg_replace('/\t/', '', $data));
    }
}
