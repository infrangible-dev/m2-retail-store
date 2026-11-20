<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Test\Unit\Helper;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Helper\Data;
use Infrangible\RetailStore\Model\ResourceModel\Store\CollectionFactory;
use Infrangible\RetailStore\Model\Store;
use Magento\Cms\Model\Block;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filter\Template\Tokenizer\Parameter;
use Magento\Widget\Model\Template\FilterFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests the markers of the stores and the CMS blocks of the templates.
 */
#[AllowMockObjectsWithoutExpectations]
class DataTest extends TestCase
{
    /** Markers with texts that contain characters that widgets and HTML would change. */
    private const MARKERS = [
        [
            'lat'         => 48.219,
            'lng'         => 11.6247,
            'title'       => 'München "Nord" & Süd',
            'info_window' => '<h3 class="name">München</h3><p>Mo-Fr {9-18} \\ \'open\' >>> <<<</p>'
        ],
        ['lat' => 51.0, 'lng' => 7.0, 'title' => 'Köln', 'info_window' => '']
    ];

    /**
     * Tests that the markers survive the parameter of a widget: Magento's tokenizer and the decoding of the widget of
     * Infrangible Google Maps return the original markers.
     *
     * @return void
     */
    public function testGetMarkerJson(): void
    {
        $markerJson = $this->createData()->getMarkerJson([]);

        $this->assertStringNotContainsString('{', $markerJson);
        $this->assertStringNotContainsString('}', $markerJson);
        $this->assertStringNotContainsString('"', $markerJson);

        $tokenizer = new Parameter();
        $tokenizer->setString(sprintf(' type="Map" marker_json="%s" zoom="6"', $markerJson));

        $parameters = $tokenizer->tokenize();

        $this->assertSame('6', $parameters[ 'zoom' ]);

        $decodedJson = str_replace(['<<<', '>>>'], ['{', '}'], htmlspecialchars_decode($parameters[ 'marker_json' ]));

        $this->assertSame(self::MARKERS, json_decode($decodedJson, true));
    }

    /**
     * Tests that the JSON of the markers contains no characters of HTML.
     *
     * @return void
     */
    public function testGetMarkersJson(): void
    {
        $markersJson = $this->createData()->getMarkersJson([]);

        $this->assertDoesNotMatchRegularExpression('/[<>&\']/', $markersJson);
        $this->assertSame(self::MARKERS, json_decode($markersJson, true));
    }

    /**
     * Tests that only active blocks of all store views or the current one are used.
     *
     * @return void
     */
    public function testGetCmsBlock(): void
    {
        $blocks = [
            1 => $this->createBlock(1, true, [0]),
            2 => $this->createBlock(2, false, [0]),
            3 => $this->createBlock(3, true, [2]),
            4 => $this->createBlock(4, true, [1, 2]),
            5 => $this->createBlock(0, true, [0])
        ];

        $data = $this->createData($blocks);

        $this->assertSame($blocks[ 1 ], $data->getCmsBlock(1));
        $this->assertNull($data->getCmsBlock(2));
        $this->assertNull($data->getCmsBlock(3));
        $this->assertSame($blocks[ 4 ], $data->getCmsBlock(4));
        $this->assertNull($data->getCmsBlock(5));
        $this->assertNull($data->getCmsBlock(0));
    }

    /**
     * Tests that the block of the store is used, otherwise the block of the configuration.
     *
     * @return void
     */
    public function testGetStoreCmsBlockId(): void
    {
        $data = $this->createData();

        $store = $this->createStub(Store::class);
        $store->method('getCmsBlockId')->willReturn(7, 0);

        $this->assertSame(7, $data->getStoreCmsBlockId($store));
        $this->assertSame(22, $data->getStoreCmsBlockId($store));
    }

    /**
     * Returns a CMS block.
     *
     * @param int $id
     * @param bool $active
     * @param int[] $storeIds
     *
     * @return Block
     */
    private function createBlock(int $id, bool $active, array $storeIds): Block
    {
        $block = $this->createStub(Block::class);

        $block->method('getId')->willReturn($id === 0 ? null : (string)$id);
        $block->method('isActive')->willReturn($active);
        $block->method('getStores')->willReturn(array_map('strval', $storeIds));

        return $block;
    }

    /**
     * Returns the helper with the CMS blocks and the markers of the constant MARKERS; the current store view has the
     * ID 1.
     *
     * @param array $blocks
     * @phpstan-param array<int, Block> $blocks
     *
     * @return Data
     */
    private function createData(array $blocks = []): Data
    {
        $cmsHelper = $this->createStub(Cms::class);
        $cmsHelper->method('loadCmsBlock')->willReturnCallback(
            fn(int $id): Block => $blocks[ $id ] ?? $this->createBlock(0, false, [])
        );

        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getStoreId')->willReturn(1);
        $storeHelper->method('getStoreConfig')->willReturn('22');

        return new TestData(
            $this->createStub(Context::class),
            new Variables(),
            $cmsHelper,
            $this->createStub(CollectionFactory::class),
            $this->createStub(Config::class),
            $this->createStub(FilterFactory::class),
            $storeHelper,
            self::MARKERS
        );
    }
}
