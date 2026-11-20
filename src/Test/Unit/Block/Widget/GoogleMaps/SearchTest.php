<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Test\Unit\Block\Widget\GoogleMaps;

use Infrangible\RetailStore\Block\Widget\GoogleMaps\Search;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template\Context;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests the values of the search form.
 */
#[AllowMockObjectsWithoutExpectations]
class SearchTest extends TestCase
{
    /**
     * Returns countries of the widget and the expected restrictions of the suggestions.
     *
     * @return array
     * @phpstan-return array<string, array{mixed, string}>
     */
    public static function countriesProvider(): array
    {
        return [
            'default'      => [null, 'country: ["de"]'],
            'list'         => [' DE, at ,ch,at', 'country: ["de","at","ch"]'],
            'invalid'      => ['de,"};alert(1);{",deu', 'country: ["de"]'],
            'at most five' => ['de,at,ch,fr,it,nl', 'country: ["de","at","ch","fr","it"]'],
            'empty'        => ['', '']
        ];
    }

    /**
     * Tests the restrictions of the suggestions.
     *
     * @dataProvider countriesProvider
     *
     * @param mixed $countries
     * @param string $expected
     *
     * @return void
     */
    #[DataProvider('countriesProvider')]
    public function testGetComponentRestrictions(mixed $countries, string $expected): void
    {
        $this->assertSame($expected, $this->createSearch(['countries' => $countries])->getComponentRestrictions());
    }

    /**
     * Tests the IDs of the CMS blocks and the selected distance.
     *
     * @return void
     */
    public function testValues(): void
    {
        $search = $this->createSearch(
            ['result_block_id' => '20', 'no_result_block_id' => 'x'],
            ['max_distance' => '25']
        );

        $this->assertSame(20, $search->getResultBlockId());
        $this->assertSame(0, $search->getNoResultBlockId());
        $this->assertSame(25, $search->getMaxDistanceValue());
        $this->assertSame(0, $this->createSearch([], ['max_distance' => ['25']])->getMaxDistanceValue());
    }

    /**
     * Returns the widget with the data and request parameters.
     *
     * @param array $data
     * @param array $parameters
     * @phpstan-param array<string, mixed> $data
     * @phpstan-param array<string, mixed> $parameters
     *
     * @return Search
     */
    private function createSearch(array $data, array $parameters = []): Search
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->willReturnCallback(fn(string $key): mixed => $parameters[ $key ] ?? null);

        $context = $this->createStub(Context::class);
        $context->method('getRequest')->willReturn($request);

        return new Search($context, array_filter($data, static fn(mixed $value): bool => $value !== null));
    }
}
