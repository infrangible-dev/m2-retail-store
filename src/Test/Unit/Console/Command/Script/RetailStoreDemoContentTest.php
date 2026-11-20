<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Test\Unit\Console\Command\Script;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Console\Command\Script\RetailStoreDemoContent;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests the content of the demo.
 */
#[AllowMockObjectsWithoutExpectations]
class RetailStoreDemoContentTest extends TestCase
{
    /**
     * Tests that the identifiers of blocks are replaced by their IDs, unknown identifiers are kept.
     *
     * @return void
     */
    public function testReplaceBlockIds(): void
    {
        $this->assertSame(
            'block_id="12" block_id="<<unknown>>" a<<b',
            $this->createScript()->replaceBlockIds(
                'block_id="<<store_search>>" block_id="<<unknown>>" a<<b',
                ['store_search' => '12']
            )
        );
    }

    /**
     * Tests that the blocks contain no placeholders of unknown blocks and that their directives are closed.
     *
     * @return void
     */
    public function testGetCmsBlockList(): void
    {
        $script = $this->createScript();
        $cmsBlockList = $script->getCmsBlockList();
        $cmsBlockIds = array_combine(array_keys($cmsBlockList), array_map('strval', range(1, count($cmsBlockList))));

        foreach ($cmsBlockList as $cmsBlockData) {
            $content = $script->replaceBlockIds($cmsBlockData[ 'content' ], $cmsBlockIds);

            $this->assertStringNotContainsString('<<', $content);
            $this->assertSame(substr_count($content, '{{'), substr_count($content, '}}'));
            $this->assertSame(substr_count($content, '{{for '), substr_count($content, '{{/for}}'));
            $this->assertSame(substr_count($content, '{{fif '), substr_count($content, '{{/fif}}'));
            $this->assertSame(substr_count($content, '{{if '), substr_count($content, '{{/if}}'));
        }
    }

    /**
     * Returns the script.
     *
     * @return RetailStoreDemoContent
     */
    private function createScript(): RetailStoreDemoContent
    {
        return new RetailStoreDemoContent(
            $this->createStub(Cms::class),
            $this->createStub(Stores::class),
            new Variables()
        );
    }
}
