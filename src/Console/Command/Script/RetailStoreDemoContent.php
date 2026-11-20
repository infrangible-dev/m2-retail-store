<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Console\Command\Script;

use Exception;
use FeWeDev\Base\Variables;
use Infrangible\Core\Console\Command\Script;
use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Stores;
use Infrangible\RetailStore\Helper\Data;
use Magento\Cms\Api\Data\BlockInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates the CMS blocks and the page "retail-stores" of the demo, unless they exist, and sets the blocks of the store
 * pages and the info windows in the configuration.
 */
class RetailStoreDemoContent extends Script
{
    /** Settings of the maps of the demo. */
    private const MAP_SETTINGS = 'zoom_control="1" map_type_control="0" street_view_control="0" rotate_control="0" ' .
        'fullscreen_control="1" administrative="1" landscape="0" poi="0" road="1" transit="1" water="1"';

    /** Widget with a CMS block, the identifier of the block is replaced by its ID. */
    private const BLOCK_WIDGET = '{{widget type="Magento\Cms\Block\Widget\Block" ' .
        'template="widget/static_block/default.phtml" block_id="<<%s>>"}}';

    /**
     * @param Cms $cmsHelper
     * @param Stores $storeHelper
     * @param Variables $variables
     */
    public function __construct(
        protected Cms $cmsHelper,
        protected Stores $storeHelper,
        protected Variables $variables
    ) {
    }

    /**
     * Creates the blocks and the page and sets the configuration.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $cmsBlockList = $this->getCmsBlockList();

        $cmsBlockIds = [];

        foreach ($cmsBlockList as $identifier => $cmsBlockData) {
            $cmsBlock = $this->cmsHelper->loadCmsBlockByIdentifier($identifier);

            if (! $cmsBlock->getId()) {
                $this->cmsHelper->importBlock(
                    [
                        BlockInterface::IDENTIFIER => $identifier,
                        BlockInterface::TITLE      => $cmsBlockData[ BlockInterface::TITLE ],
                        BlockInterface::CONTENT    => $cmsBlockData[ BlockInterface::CONTENT ]
                    ]
                );

                $cmsBlock = $this->cmsHelper->loadCmsBlockByIdentifier($identifier);

                $output->writeln(sprintf('Created CMS block: %s', $identifier));
            }

            $cmsBlockIds[ $identifier ] = (string)$cmsBlock->getId();
        }

        foreach (array_keys($cmsBlockList) as $identifier) {
            $cmsBlock = $this->cmsHelper->loadCmsBlockByIdentifier($identifier);
            $content = $this->variables->stringValueOrDefault($cmsBlock->getData(BlockInterface::CONTENT));
            $replacedContent = $this->replaceBlockIds($content, $cmsBlockIds);

            if ($cmsBlock->getId() && $replacedContent !== $content) {
                $cmsBlock->setContent($replacedContent);

                $this->cmsHelper->saveCmsBlock($cmsBlock);
            }
        }

        $cmsPage = $this->cmsHelper->loadCmsPageByIdentifier('retail-stores');

        if (! $cmsPage->getId()) {
            $this->cmsHelper->importPage(
                [
                    'identifier'  => 'retail-stores',
                    'title'       => __('Retail Stores')->render(),
                    'page_layout' => '1column',
                    'content'     => $this->replaceBlockIds(
                        '<div>' . sprintf(self::BLOCK_WIDGET, 'store_search') . '</div>' . "\n" .
                        '<div>' . sprintf(self::BLOCK_WIDGET, 'store_map') . '</div>',
                        $cmsBlockIds
                    )
                ]
            );

            $output->writeln('Created CMS page: retail-stores');
        }

        $this->storeHelper->insertConfigValue(Data::CONFIG_CMS_BLOCK_ID, $cmsBlockIds[ 'store_detail_page' ]);
        $this->storeHelper->insertConfigValue(
            Data::CONFIG_INFO_WINDOW_CMS_BLOCK_ID,
            $cmsBlockIds[ 'store_info_window' ]
        );

        return 0;
    }

    /**
     * Replaces the identifiers of CMS blocks in double angle brackets (<<identifier>>) with their IDs.
     *
     * @param string $content
     * @param array $cmsBlockIds
     * @phpstan-param array<string, string> $cmsBlockIds
     *
     * @return string
     */
    public function replaceBlockIds(string $content, array $cmsBlockIds): string
    {
        return (string)preg_replace_callback(
            '/<<([a-z0-9_]+)>>/',
            static fn(array $matches): string => $cmsBlockIds[ $matches[ 1 ] ] ?? $matches[ 0 ],
            $content
        );
    }

    /**
     * Returns the title and content of the CMS blocks by identifier.
     *
     * @return array
     * @phpstan-return array<string, array<string, string>>
     */
    public function getCmsBlockList(): array
    {
        $line = static fn(string $class, string $label, string $value): string => sprintf(
            '<div class="info-window-line info-window-%s"><span class="label">#trans "%s"#:</span> %s</div>',
            $class,
            $label,
            $value
        );

        return [
            'store_map'         => [
                BlockInterface::TITLE   => 'Store Map',
                BlockInterface::CONTENT => '<p>' .
                    '{{widget type="Infrangible\RetailStore\Block\Widget\GoogleMaps\Stores" map_id="all_stores" ' .
                    'width="1240" height="620" zoom="6" scale_control="1" ' . self::MAP_SETTINGS . '}}</p>'
            ],
            'store_search'      => [
                BlockInterface::TITLE   => 'Store Search',
                BlockInterface::CONTENT => '<div class="store-search">' .
                    '{{widget type="Infrangible\RetailStore\Block\Widget\GoogleMaps\Search" ' .
                    'result_block_id="<<store_result>>" no_result_block_id="<<store_no_result>>" countries="de"}}</div>'
            ],
            'store_result'      => [
                BlockInterface::TITLE   => 'Store Result',
                BlockInterface::CONTENT => '<div>' . sprintf(self::BLOCK_WIDGET, 'store_search') . '</div>' . "\n" .
                    '<div class="store-map">' . "\n" .
                    '<p>{{widget type="Infrangible\GoogleMaps\Block\Widget\GoogleMaps\JsonMarker" ' .
                    'map_id="search_result" width="1240" height="620" zoom="9" scale_control="1" ' .
                    self::MAP_SETTINGS . ' marker_json="#var marker_json#"}}</p>' . "\n" .
                    '</div>' . "\n" .
                    '<div class="store-list">' . "\n" .
                    '{{for retail_store in retail_stores}}' . "\n" .
                    '<div>' . "\n" .
                    '<h3>{{var retail_store.name}}</h3>' . "\n" .
                    $line(
                        'address',
                        'Address',
                        '{{var retail_store.street}}, {{var retail_store.postcode}} {{var retail_store.city}}'
                    ) . "\n" .
                    $line('phone', 'Phone Number', '{{var retail_store.phone_number}}') . "\n" .
                    $line('mail', 'E-Mail', '{{var retail_store.email}}') . "\n" .
                    '{{fif retail_store.has_opening_hours}}' .
                    $line('opening-hours', 'Opening Hours', '{{var retail_store.opening_hours}}') .
                    '{{/fif}}' . "\n" .
                    '{{fif retail_store.has_opening_hours_special}}' .
                    $line('opening-hours', 'Special Opening Hours', '{{var retail_store.opening_hours_special}}') .
                    '{{/fif}}' . "\n" .
                    '<div class="info-window-link"><a href="{{var retail_store.url}}" ' .
                    'class="action primary btn btn-primary">#trans "Store Detail Page"#</a></div>' . "\n" .
                    '</div>' . "\n" .
                    '{{/for}}' . "\n" .
                    '</div>'
            ],
            'store_no_result'   => [
                BlockInterface::TITLE   => 'Store No Result',
                BlockInterface::CONTENT => '<div>{{trans "No retail stores were found."}}</div>' . "\n" .
                    '<hr/>' . "\n" .
                    '<div>' . sprintf(self::BLOCK_WIDGET, 'store_search') . '</div>'
            ],
            'store_detail_page' => [
                BlockInterface::TITLE   => 'Store Detail Page',
                BlockInterface::CONTENT =>
                    $line('address', 'Address', '{{var street}}, {{var postcode}} {{var city}}') . "\n" .
                    $line('phone', 'Phone Number', '{{var phone_number}}') . "\n" .
                    $line('mail', 'E-Mail', '{{var email}}') . "\n" .
                    '{{if has_opening_hours}}' .
                    $line('opening-hours', 'Opening Hours', '{{var opening_hours}}') .
                    '{{/if}}' . "\n" .
                    '{{if has_opening_hours_special}}' .
                    $line('opening-hours', 'Special Opening Hours', '{{var opening_hours_special}}') .
                    '{{/if}}' . "\n" .
                    '{{widget type="Infrangible\GoogleMaps\Block\Widget\GoogleMaps\SimpleMarker" ' .
                    'map_id="retail_store_id" height="500" lat="#var latitude#" lng="#var longitude#" ' .
                    'title="#var name#" zoom="13" scale_control="0" ' . self::MAP_SETTINGS . '}}'
            ],
            'store_info_window' => [
                BlockInterface::TITLE   => 'Store Info Window',
                BlockInterface::CONTENT => '<h3>{{var name}}</h3>' . "\n" .
                    $line('address', 'Address', '{{var street}}, {{var postcode}} {{var city}}') . "\n" .
                    $line('phone', 'Phone Number', '{{var phone_number}}') . "\n" .
                    $line('mail', 'E-Mail', '{{var email}}') . "\n" .
                    '{{if has_opening_hours}}' .
                    $line('opening-hours', 'Opening Hours', '{{var opening_hours}}') .
                    '{{/if}}' . "\n" .
                    '<div class="info-window-link"><a href="{{var url}}" class="action primary btn btn-primary">' .
                    '#trans "Store Detail Page"#</a></div>'
            ]
        ];
    }
}
