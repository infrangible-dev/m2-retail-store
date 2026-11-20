<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Test\Unit\Controller;

use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Controller\Router;
use Infrangible\RetailStore\Helper\Data;
use Infrangible\RetailStore\Model\Store;
use Magento\Cms\Model\Block;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Tests the forwarding of the URLs of the stores.
 */
#[AllowMockObjectsWithoutExpectations]
class RouterTest extends TestCase
{
    /**
     * Returns paths, URL suffixes and the URL key of the store that is loaded, or null if no store is loaded.
     *
     * @return array
     * @phpstan-return array<string, array{string, string|null, string|null}>
     */
    public static function pathProvider(): array
    {
        return [
            'with suffix'          => ['/munich.html', '.html', 'munich'],
            'without suffix'       => ['/munich', null, 'munich'],
            'missing suffix'       => ['/munich', '.html', null],
            'other suffix'         => ['/munich.htm', '.html', null],
            'encoded'              => ['/m%C3%BCnchen.html', '.html', 'münchen'],
            'empty suffix in path' => ['/.html', '.html', '']
        ];
    }

    /**
     * Tests which URL key is searched for the path.
     *
     * @dataProvider pathProvider
     *
     * @param string $path
     * @param string|null $suffix
     * @param string|null $urlKey
     *
     * @return void
     * @throws LocalizedException
     */
    #[DataProvider('pathProvider')]
    public function testMatchUrlKey(string $path, ?string $suffix, ?string $urlKey): void
    {
        $retailStoreHelper = $this->createMock(Data::class);

        if ($urlKey === null) {
            $retailStoreHelper->expects($this->never())->method('loadRetailStoreWithKey');
        } else {
            $retailStoreHelper->expects($this->once())->method('loadRetailStoreWithKey')->with($urlKey)->willReturn(
                null
            );
        }

        $this->assertNull($this->createRouter($retailStoreHelper, $suffix)->match($this->createRequest($path)));
    }

    /**
     * Tests that the request of a store with a CMS block is forwarded to the store view.
     *
     * @return void
     * @throws LocalizedException
     */
    public function testMatchForward(): void
    {
        $store = $this->createStub(Store::class);
        $store->method('getId')->willReturn('5');

        $retailStoreHelper = $this->createStub(Data::class);
        $retailStoreHelper->method('loadRetailStoreWithKey')->willReturn($store);
        $retailStoreHelper->method('getStoreCmsBlockId')->willReturn(3);
        $retailStoreHelper->method('getCmsBlock')->willReturn($this->createStub(Block::class));

        $request = $this->createMock(Http::class);
        $request->method('getPathInfo')->willReturn('/munich.html');
        $request->expects($this->once())->method('setModuleName')->with('retail_store');
        $request->expects($this->once())->method('setControllerName')->with('store');
        $request->expects($this->once())->method('setActionName')->with('view');
        $request->expects($this->once())->method('setParam')->with('retail_store_id', '5');

        $this->assertInstanceOf(
            Forward::class,
            $this->createRouter($retailStoreHelper, '.html')->match($request)
        );
    }

    /**
     * Tests that stores without an available CMS block are not forwarded.
     *
     * @return void
     */
    public function testMatchWithoutCmsBlock(): void
    {
        $retailStoreHelper = $this->createStub(Data::class);
        $retailStoreHelper->method('loadRetailStoreWithKey')->willReturn($this->createStub(Store::class));
        $retailStoreHelper->method('getCmsBlock')->willReturn(null);

        $this->assertNull(
            $this->createRouter($retailStoreHelper, '.html')->match($this->createRequest('/munich.html'))
        );
    }

    /**
     * Returns a request with the path.
     *
     * @param string $path
     *
     * @return Http
     */
    private function createRequest(string $path): Http
    {
        $request = $this->createStub(Http::class);
        $request->method('getPathInfo')->willReturn($path);

        return $request;
    }

    /**
     * Returns the router with the helper and the URL suffix of categories.
     *
     * @param Data $retailStoreHelper
     * @param string|null $suffix
     *
     * @return Router
     */
    private function createRouter(Data $retailStoreHelper, ?string $suffix): Router
    {
        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getStoreConfig')->willReturn($suffix);

        $actionFactory = $this->createStub(ActionFactory::class);
        $actionFactory->method('create')->willReturn($this->createStub(Forward::class));

        return new Router($storeHelper, $retailStoreHelper, $actionFactory, $this->createStub(LoggerInterface::class));
    }
}
