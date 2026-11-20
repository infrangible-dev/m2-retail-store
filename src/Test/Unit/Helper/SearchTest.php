<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Test\Unit\Helper;

use ArrayIterator;
use FeWeDev\Base\Arrays;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Stores;
use Infrangible\GoogleMaps\Model\GeocodingApi;
use Infrangible\RetailStore\Helper\Search;
use Infrangible\RetailStore\Model\ResourceModel\Store\Collection;
use Infrangible\RetailStore\Model\ResourceModel\Store\CollectionFactory;
use Infrangible\RetailStore\Model\Store;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests the search of stores by distance.
 */
#[AllowMockObjectsWithoutExpectations]
class SearchTest extends TestCase
{
    /**
     * Tests the distance between Munich and Augsburg, about 57 kilometers.
     *
     * @return void
     */
    public function testCalculateDistance(): void
    {
        $search = $this->createSearch([]);

        $this->assertEqualsWithDelta(57.0, $search->calculateDistance(48.1372, 11.5756, 48.3705, 10.8978), 1.0);
        $this->assertSame(0.0, $search->calculateDistance(48.1372, 11.5756, 48.1372, 11.5756));
    }

    /**
     * Tests that the stores are sorted by distance, limited by number and distance, and that stores without
     * coordinates and excluded stores are skipped.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testSearchRetailStoresWithCoordinates(): void
    {
        $stores = [
            $this->createStore(1, 48.3705, 10.8978),
            $this->createStore(2, 48.1372, 11.5756),
            $this->createStore(3, 52.5200, 13.4050),
            $this->createStore(4, null, null),
            $this->createStore(5, 48.1500, 11.5800)
        ];

        $search = $this->createSearch($stores);

        $this->assertSame([2, 5, 1, 3], $this->getIds($search->searchRetailStoresWithCoordinates(48.1372, 11.5756)));
        $this->assertSame([2, 5], $this->getIds($search->searchRetailStoresWithCoordinates(48.1372, 11.5756, 2)));
        $this->assertSame(
            [2, 5, 1],
            $this->getIds($search->searchRetailStoresWithCoordinates(48.1372, 11.5756, 0, 100))
        );
        $this->assertSame(
            [5, 1],
            $this->getIds($search->searchRetailStoresWithCoordinates(48.1372, 11.5756, 2, 0, [2]))
        );
    }

    /**
     * Tests that a location without coordinates finds no stores.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testSearchRetailStoresWithLocation(): void
    {
        $search = $this->createSearch([$this->createStore(1, 48.1372, 11.5756)], ['geometry' => ['location' => []]]);

        $this->assertSame([], $search->searchRetailStoresWithLocation('Munich'));
        $this->assertSame([], $search->searchRetailStoresWithLocation(' '));

        $search = $this->createSearch(
            [$this->createStore(1, 48.1372, 11.5756)],
            ['geometry' => ['location' => ['lat' => 48.14, 'lng' => 11.58]]]
        );

        $this->assertSame([1], $this->getIds($search->searchRetailStoresWithLocation('Munich')));
    }

    /**
     * Returns the IDs of the stores.
     *
     * @param Store[] $stores
     *
     * @return int[]
     */
    private function getIds(array $stores): array
    {
        $variables = new Variables();

        return array_map(static fn(Store $store): int => $variables->intValue($store->getId()), $stores);
    }

    /**
     * Returns a store with the ID and coordinates.
     *
     * @param int $id
     * @param float|null $latitude
     * @param float|null $longitude
     *
     * @return Store
     */
    private function createStore(int $id, ?float $latitude, ?float $longitude): Store
    {
        $store = $this->createStub(Store::class);

        $store->method('getId')->willReturn((string)$id);
        $store->method('getLatitude')->willReturn($latitude);
        $store->method('getLongitude')->willReturn($longitude);

        return $store;
    }

    /**
     * Returns the search with the stores and the result of the Geocoding API.
     *
     * @param Store[] $stores
     * @param array $searchResult
     * @phpstan-param array<string, mixed> $searchResult
     *
     * @return Search
     */
    private function createSearch(array $stores, array $searchResult = []): Search
    {
        $collection = $this->createStub(Collection::class);
        $collection->method('getIterator')->willReturn(new ArrayIterator($stores));

        $collectionFactory = $this->createStub(CollectionFactory::class);
        $collectionFactory->method('create')->willReturn($collection);

        $googleApi = $this->createStub(GeocodingApi::class);
        $googleApi->method('searchLocation')->willReturn($searchResult);

        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('formatNumber')->willReturnCallback(
            static fn(float $value): string => number_format($value, 2)
        );

        $variables = new Variables();

        return new Search($storeHelper, $googleApi, $collectionFactory, new Arrays($variables), $variables);
    }
}
