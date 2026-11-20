<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Helper;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Stores;
use Infrangible\GoogleMaps\Model\GeocodingApi;
use Infrangible\RetailStore\Model\ResourceModel\Store\CollectionFactory;
use Infrangible\RetailStore\Model\Store;
use Magento\Framework\Exception\LocalizedException;

/**
 * Searches the active retail stores near a location or coordinates.
 */
class Search
{
    /** Mean radius of the earth in meters. */
    public const EARTH_RADIUS = 6371000;

    /**
     * @param Stores $storeHelper
     * @param GeocodingApi $googleApi
     * @param CollectionFactory $retailStoreCollectionFactory
     * @param Arrays $arrays
     * @param Variables $variables
     */
    public function __construct(
        protected Stores $storeHelper,
        protected GeocodingApi $googleApi,
        protected CollectionFactory $retailStoreCollectionFactory,
        protected Arrays $arrays,
        protected Variables $variables
    ) {
    }

    /**
     * Returns the active stores near the location, sorted by distance.
     *
     * The location is searched with the Geocoding API of Google Maps.
     *
     * @param string $location
     * @param int $maxNumber the maximum number of stores, 0 for all
     * @param int $maxDistance the maximum distance in kilometers, 0 for any distance
     *
     * @return Store[]
     * @throws LocalizedException
     */
    public function searchRetailStoresWithLocation(string $location, int $maxNumber = 0, int $maxDistance = 0): array
    {
        $location = trim($location);

        if ($location === '') {
            return [];
        }

        $searchResult = $this->googleApi->searchLocation($location);

        $latitude = $this->arrays->getValue($searchResult, 'geometry:location:lat');
        $longitude = $this->arrays->getValue($searchResult, 'geometry:location:lng');

        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return [];
        }

        return $this->searchRetailStoresWithCoordinates((float)$latitude, (float)$longitude, $maxNumber, $maxDistance);
    }

    /**
     * Returns the active stores near the coordinates with all attributes, sorted by distance.
     *
     * The distance in kilometers is set as data "distance", formatted for the current store view.
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $maxNumber the maximum number of stores, 0 for all
     * @param int $maxDistance the maximum distance in kilometers, 0 for any distance
     * @param int[] $excludedRetailStoreIds
     *
     * @return Store[]
     * @throws LocalizedException
     */
    public function searchRetailStoresWithCoordinates(
        float $latitude,
        float $longitude,
        int $maxNumber = 0,
        int $maxDistance = 0,
        array $excludedRetailStoreIds = []
    ): array {
        $retailStoreCollection = $this->retailStoreCollectionFactory->create();

        $retailStoreCollection->addAttributeToSelect('*');
        $retailStoreCollection->addActiveFilter();

        $retailStoreDistances = [];
        $retailStores = [];

        foreach ($retailStoreCollection as $retailStore) {
            if (! $retailStore instanceof Store) {
                continue;
            }

            $retailStoreId = $this->variables->intValue($retailStore->getId());

            if (in_array($retailStoreId, $excludedRetailStoreIds, true)) {
                continue;
            }

            $retailStoreLatitude = $retailStore->getLatitude();
            $retailStoreLongitude = $retailStore->getLongitude();

            if ($retailStoreLatitude === null || $retailStoreLongitude === null) {
                continue;
            }

            $distance = $this->calculateDistance($latitude, $longitude, $retailStoreLatitude, $retailStoreLongitude);

            if ($maxDistance <= 0 || $distance <= $maxDistance) {
                $retailStoreDistances[ $retailStoreId ] = $distance;
                $retailStores[ $retailStoreId ] = $retailStore;
            }
        }

        asort($retailStoreDistances);

        if ($maxNumber > 0) {
            $retailStoreDistances = array_slice($retailStoreDistances, 0, $maxNumber, true);
        }

        $result = [];

        foreach ($retailStoreDistances as $retailStoreId => $distance) {
            $retailStore = $retailStores[ $retailStoreId ];

            $retailStore->setData('distance', $this->storeHelper->formatNumber($distance));

            $result[] = $retailStore;
        }

        return $result;
    }

    /**
     * Returns the distance between the coordinates in kilometers (great-circle distance with the Vincenty formula).
     *
     * @param float $latitude1
     * @param float $longitude1
     * @param float $latitude2
     * @param float $longitude2
     *
     * @return float
     */
    public function calculateDistance(float $latitude1, float $longitude1, float $latitude2, float $longitude2): float
    {
        $latFrom = deg2rad($latitude1);
        $lonFrom = deg2rad($longitude1);
        $latTo = deg2rad($latitude2);
        $lonTo = deg2rad($longitude2);

        $lonDelta = $lonTo - $lonFrom;
        $a = (cos($latTo) * sin($lonDelta)) ** 2 +
            (cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta)) ** 2;
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        return ($angle * static::EARTH_RADIUS) / 1000;
    }
}
