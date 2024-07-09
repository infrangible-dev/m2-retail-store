<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Helper;

use FeWeDev\Base\Arrays;
use Infrangible\Core\Helper\Stores;
use Infrangible\GoogleMaps\Model\GeocodingApi;
use Infrangible\RetailStore\Model\ResourceModel\Store\CollectionFactory;
use Infrangible\RetailStore\Model\Store;
use Infrangible\RetailStore\Model\StoreFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Search
{
    public const EARTH_RADIUS = 6371000;

    /** @var Stores */
    protected $storeHelper;

    /** @var GeocodingApi */
    protected $googleApi;

    /** @var StoreFactory */
    protected $retailStoreFactory;

    /** @var \Infrangible\RetailStore\Model\ResourceModel\StoreFactory */
    protected $retailStoreResourceFactory;

    /** @var CollectionFactory */
    protected $retailStoreCollectionFactory;

    /** @var Arrays */
    protected $arrays;

    public function __construct(
        Stores $storeHelper,
        GeocodingApi $googleApi,
        StoreFactory $retailStoreFactory,
        \Infrangible\RetailStore\Model\ResourceModel\StoreFactory $retailStoreResourceFactory,
        CollectionFactory $retailStoreCollectionFactory,
        Arrays $arrays)
    {
        $this->storeHelper = $storeHelper;

        $this->googleApi = $googleApi;
        $this->retailStoreFactory = $retailStoreFactory;
        $this->retailStoreResourceFactory = $retailStoreResourceFactory;
        $this->retailStoreCollectionFactory = $retailStoreCollectionFactory;
        $this->arrays = $arrays;
    }

    /**
     * @param string $location
     * @param int $maxNumber
     * @param int $maxDistance
     *
     * @return Store[]
     * @throws LocalizedException
     */
    public function searchRetailStoresWithLocation(
        string $location,
        int $maxNumber = 0,
        int $maxDistance = 99999999): array
    {
        $coordinates = $this->googleApi->searchLocation($location);

        if (empty($coordinates)) {
            return [];
        }

        $location = $this->arrays->getValue($coordinates, 'geometry:location', []);

        $latitude = $this->arrays->getValue($location, 'lat');
        $longitude = $this->arrays->getValue($location, 'lng');

        return $this->searchRetailStoresWithCoordinates($latitude, $longitude, $maxNumber, $maxDistance);
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int $maxNumber
     * @param int $maxDistance
     *
     * @return Store[]
     * @throws LocalizedException
     */
    public function searchRetailStoresWithCoordinates(
        float $latitude,
        float $longitude,
        int $maxNumber = 0,
        int $maxDistance = 99999999): array
    {
        $retailStoreCollection = $this->retailStoreCollectionFactory->create();

        $retailStoreCollection->addAttributeToSelect('latitude');
        $retailStoreCollection->addAttributeToSelect('longitude');
        $retailStoreCollection->addFieldToFilter('status', ['eq' => 1]);

        $retailStoreCollection->load();

        $retailStoreDistances = [];

        /** @var Store $retailStore */
        foreach ($retailStoreCollection as $retailStore) {
            $distance = $this->calculateDistance($latitude, $longitude, floatval($retailStore->getLatitude()),
                floatval($retailStore->getLongitude()));

            if ($distance <= $maxDistance) {
                $retailStoreDistances[$retailStore->getId()] = $distance;
            }
        }

        asort($retailStoreDistances);

        $retailStoreIds = array_keys($retailStoreDistances);

        if ($maxNumber > 0) {
            $retailStoreIds = array_splice($retailStoreIds, 0, $maxNumber);
        }

        $result = [];

        $retailStoreResource = $this->retailStoreResourceFactory->create();

        foreach ($retailStoreIds as $retailStoreId) {
            $retailStore = $this->retailStoreFactory->create();

            $retailStoreResource->load($retailStore, $retailStoreId);

            $distance = $retailStoreDistances[$retailStore->getId()];

            $retailStore->setData('distance', $this->storeHelper->formatNumber($distance));

            $result[] = $retailStore;
        }

        return $result;
    }

    /**
     * @param float $latitude1
     * @param float $longitude1
     * @param float $latitude2
     * @param float $longitude2
     *
     * @return float
     */
    public function calculateDistance(float $latitude1, float $longitude1, float $latitude2, float $longitude2)
    {
        $latFrom = deg2rad($latitude1);
        $lonFrom = deg2rad($longitude1);
        $latTo = deg2rad($latitude2);
        $lonTo = deg2rad($longitude2);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);

        //distance in km
        return ($angle * static::EARTH_RADIUS) / 1000;
    }
}
