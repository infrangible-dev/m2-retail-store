<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Test\Unit\Model;

use Infrangible\Core\Helper\Stores;
use Infrangible\Core\Helper\Url;
use Infrangible\RetailStore\Model\ResourceModel\Feature\CollectionFactory;
use Infrangible\RetailStore\Model\ResourceModel\Store as StoreResource;
use Infrangible\RetailStore\Model\Store;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests the values of a store.
 */
#[AllowMockObjectsWithoutExpectations]
class StoreTest extends TestCase
{
    /**
     * Tests the IDs of the features, stored as comma-separated list or array.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testGetFeatureIds(): void
    {
        $this->assertSame([1, 3], $this->createStore(['feature' => '1,0,3,x'])->getFeatureIds());
        $this->assertSame([2], $this->createStore(['feature' => ['2', '']])->getFeatureIds());
        $this->assertSame([4], $this->createStore(['feature' => 4])->getFeatureIds());
        $this->assertSame([], $this->createStore([])->getFeatureIds());
        $this->assertSame([], $this->createStore([])->getFeatures());
    }

    /**
     * Tests the typed values.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testValues(): void
    {
        $store = $this->createStore(
            ['name' => 'Munich', 'latitude' => '48.2190', 'longitude' => 'x', 'cms_block_id' => '12', 'status' => '1']
        );

        $this->assertSame('Munich', $store->getName());
        $this->assertSame(48.219, $store->getLatitude());
        $this->assertNull($store->getLongitude());
        $this->assertSame(12, $store->getCmsBlockId());
        $this->assertTrue($store->isActive());
        $this->assertSame('', $store->getDescription());

        $store = $this->createStore(['status' => '0', 'cms_block_id' => null]);

        $this->assertFalse($store->isActive());
        $this->assertSame(0, $store->getCmsBlockId());
    }

    /**
     * Tests that the path is the URL key with the URL suffix of categories.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testGetUrlPath(): void
    {
        $this->assertSame('munich.html', $this->createStore(['url_key' => 'munich'], '.html')->getUrlPath());
        $this->assertSame('munich', $this->createStore(['url_key' => 'munich'])->getUrlPath());
    }

    /**
     * Returns a store with the data and the URL suffix of categories.
     *
     * @param array $data
     * @param string|null $urlSuffix
     * @phpstan-param array<string, mixed> $data
     *
     * @return Store
     * @throws LocalizedException
     */
    private function createStore(array $data, ?string $urlSuffix = null): Store
    {
        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getStoreConfig')->willReturn($urlSuffix);

        /** @noinspection PhpDeprecationInspection, PhpFullyQualifiedNameUsageInspection the models need the registry */
        $registry = $this->createStub(\Magento\Framework\Registry::class);

        return new Store(
            $this->createStub(Context::class),
            $registry,
            $storeHelper,
            $this->createStub(Url::class),
            $this->createStub(CollectionFactory::class),
            $this->createStub(StoreResource::class),
            null,
            $data
        );
    }
}
