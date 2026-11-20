<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Block\Widget\GoogleMaps;

use Infrangible\GoogleMaps\Block\Widget\GoogleMaps\Autocomplete;
use Infrangible\RetailStore\Block\Widget\GoogleMaps\Search\Buttons;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Widget with the search form: a location with the suggestions of Google Maps and the maximum distance.
 */
class Search extends Template implements BlockInterface
{
    /** Countries of the suggestions if the widget has none. */
    public const DEFAULT_COUNTRIES = 'de';

    /**
     * Sets the template.
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->setTemplate('Infrangible_RetailStore::search.phtml');

        parent::_construct();
    }

    /**
     * Returns the input of the location with the suggestions of Google Maps.
     *
     * @return string
     */
    public function getLocationInput(): string
    {
        try {
            $locationInputBlock = $this->getLayout()->createBlock(Autocomplete::class);

            if (! $locationInputBlock instanceof Autocomplete) {
                return '';
            }

            $locationInputBlock->setData('input_id', 'location');
            $locationInputBlock->setData('component_restrictions', $this->getComponentRestrictions());

            return $locationInputBlock->toHtml();
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception);

            return '';
        }
    }

    /**
     * Returns the codes of the countries of the suggestions (ISO 3166-1 alpha-2), at most five.
     *
     * @return string[]
     */
    public function getCountries(): array
    {
        $countries = $this->getData('countries');

        if (! is_string($countries)) {
            $countries = self::DEFAULT_COUNTRIES;
        }

        $countries = array_map(
            static fn(string $country): string => strtolower(trim($country)),
            explode(',', $countries)
        );

        $countries = array_filter(
            $countries,
            static fn(string $country): bool => (bool)preg_match('/^[a-z]{2}$/', $country)
        );

        return array_slice(array_values(array_unique($countries)), 0, 5);
    }

    /**
     * Returns the restrictions of the suggestions for the autocomplete widget of Infrangible Google Maps.
     *
     * @return string
     */
    public function getComponentRestrictions(): string
    {
        $countries = $this->getCountries();

        return count($countries) > 0 ? sprintf('country: %s', json_encode($countries)) : '';
    }

    /**
     * Returns the distances to select, in kilometers with their labels.
     *
     * @return array
     * @phpstan-return array<int, string>
     */
    public function getDistances(): array
    {
        return [10 => '10 km', 25 => '25 km', 50 => '50 km', 100 => '100 km'];
    }

    /**
     * Returns the selected distance of the request.
     *
     * @return int
     */
    public function getMaxDistanceValue(): int
    {
        $maxDistance = $this->getRequest()->getParam('max_distance');

        return is_numeric($maxDistance) ? (int)$maxDistance : 0;
    }

    /**
     * Returns the ID of the result CMS block.
     *
     * @return int
     */
    public function getResultBlockId(): int
    {
        $resultBlockId = $this->getData('result_block_id');

        return is_numeric($resultBlockId) ? (int)$resultBlockId : 0;
    }

    /**
     * Returns the ID of the CMS block if no stores are found.
     *
     * @return int
     */
    public function getNoResultBlockId(): int
    {
        $noResultBlockId = $this->getData('no_result_block_id');

        return is_numeric($noResultBlockId) ? (int)$noResultBlockId : 0;
    }

    /**
     * Returns the URL of the search.
     *
     * @return string
     */
    public function getSearchUrl(): string
    {
        return $this->getUrl('retail_store/store/search');
    }

    /**
     * Returns the buttons of the form.
     *
     * @return string
     */
    public function getButtonsHtml(): string
    {
        try {
            return $this->getLayout()->createBlock(Buttons::class)->toHtml();
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception);

            return '';
        }
    }
}
