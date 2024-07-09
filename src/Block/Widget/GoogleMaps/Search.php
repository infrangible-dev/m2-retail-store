<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Block\Widget\GoogleMaps;

use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\Autocomplete;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Search
    extends Template
    implements BlockInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('Infrangible_RetailStore::search.phtml');

        parent::_construct();
    }

    public function getLocationInput(): string
    {
        /** @var  $locationInputBlock */
        try {
            /** @var Autocomplete $locationInputBlock */
            $locationInputBlock = $this->getLayout()->createBlock(Autocomplete::class);

            $locationInputBlock->setData('input_id', 'location');
            $locationInputBlock->setData('component_restrictions', 'country:"de"');

            return $locationInputBlock->toHtml();
        } catch (LocalizedException $exception) {
            return '';
        }
    }

    /**
     * @return string[]
     */
    public function getDistances(): array
    {
        return [10 => '10 km', 25 => '25 km', 50 => '50 km', 100 => '100 km'];
    }

    public function getMaxDistanceValue(): ?string
    {
        $request = $this->getRequest();

        return $request->getParam('max_distance');
    }

    public function getResultBlockId(): int
    {
        return (int)$this->getData('result_block_id');
    }

    public function getNoResultBlockId(): int
    {
        return (int)$this->getData('no_result_block_id');
    }
}
