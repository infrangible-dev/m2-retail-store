<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Console\Command\Script;

use Infrangible\Core\Console\Command\Script;
use Infrangible\Core\Helper\Cms;
use Infrangible\Core\Helper\Stores;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class RetailStoreDemoContent
    extends Script
{
    /** @var Cms */
    protected $cmsHelper;

    /** @var Stores */
    protected $storeHelper;

    public function __construct(Cms $cmsHelper, Stores $storeHelper)
    {
        $this->cmsHelper = $cmsHelper;
        $this->storeHelper = $storeHelper;
    }

    /**
     * @throws AlreadyExistsException
     * @throws LocalizedException
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $apiKey = $this->storeHelper->getStoreConfig('infrangible_googlemaps/settings/api_key');

        if (!$apiKey) {
            throw new \Exception('No Google Maps API key');
        }

        $cmsBlockList = [[BlockInterface::IDENTIFIER => 'store_map',
            BlockInterface::TITLE => 'Store Map',
            BlockInterface::CONTENT => '<p>{{widget type="Infrangible\RetailStore\Block\Widget\GoogleMaps\Stores" map_id="all_stores" width="1240" height="620" zoom="6" zoom_control="1" map_type_control="0" street_view_control="0" rotate_control="0" scale_control="1" fullscreen_control="1" administrative="1" landscape="0" poi="0" road="1" transit="1" water="1"}}</p>'],
            [BlockInterface::IDENTIFIER => 'store_search',
                BlockInterface::TITLE => 'Store Search',
                BlockInterface::CONTENT => '<div class="store-search">{{widget type="Infrangible\RetailStore\Block\Widget\GoogleMaps\Search" result_block_id="<<store_result>>" no_result_block_id="<<store_no_result>>"}}</div>'],
            [BlockInterface::IDENTIFIER => 'store_result',
                BlockInterface::TITLE => 'Store Result',
                BlockInterface::CONTENT => '<div>{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="<<store_search>>"}}</div>
<div class="store-map">
<p>{{widget type="Infrangible\GoogleMaps\Block\Widget\GoogleMaps\JsonMarker" map_id="search_result" width="1240" height="620" zoom="9" zoom_control="1" map_type_control="0" street_view_control="0" rotate_control="0" scale_control="1" fullscreen_control="1" administrative="1" landscape="0" poi="0" road="1" transit="1" water="1" marker_json="#var marker_json#"}}</p>
</div>
<div class="store-list">
  {{for retail_store in retail_stores}}
    <div>
      <h3>{{var retail_store.name}}</h3>
      <div class="info-window-line info-window-address">
        <span class="label">#trans "Address"#:</span>
        {{var retail_store.street}}, {{var retail_store.postcode}} {{var retail_store.city}}
      </div>
      <div class="info-window-line info-window-phone">
        <span class="label">#trans "Phone Number"#:</span>
        {{var retail_store.phone_number}}
      </div>
      <div class="info-window-line info-window-mail">
        <span class="label">#trans "E-Mail"#:</span>
        {{var retail_store.email}}
      </div>
      {{fif retail_store.has_opening_hours}}
      <div class="info-window-line info-window-opening-hours">
        <span class="label">#trans "Opening Hours"#:</span>
        {{var retail_store.opening_hours}}
      </div>
      {{/fif}}
      {{fif retail_store.has_opening_hours_special}}
      <div class="info-window-line info-window-opening-hours">
        <span class="label">#trans ""Special Opening Hours""#:</span>
        {{var retail_store.opening_hours_special}}
      </div>
      {{/fif}}
      <div class="info-window-link">
        <a href="{{var retail_store.url}}" class="action primary">#trans "Store Detail Page"#</a>
      </div>
    </div>
  {{/for}}
</div>'],
            [BlockInterface::IDENTIFIER => 'store_no_result',
                BlockInterface::TITLE => 'Store No Result',
                BlockInterface::CONTENT => '<div>{{trans "No retail stores were found."}}</div>
<hr />
<div>{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="<<store_search>>"}}</div>'],
            [BlockInterface::IDENTIFIER => 'store_detail_page',
                BlockInterface::TITLE => 'Store Detail Page',
                BlockInterface::CONTENT => '<div class="info-window-line info-window-address">
  <span class="label">#trans "Address"#:</span>
  {{var street}}, {{var postcode}} {{var city}}
</div>
<div class="info-window-line info-window-phone">
  <span class="label">#trans "Phone Number"#:</span>
  {{var phone_number}}
</div>
<div class="info-window-line info-window-mail">
  <span class="label">#trans "E-Mail"#:</span>
  {{var email}}
</div>
{{if has_opening_hours}}
<div class="info-window-line info-window-opening-hours">
  <span class="label">#trans "Opening Hours"#:</span>
  {{var opening_hours}}
</div>
{{/if}}
{{if has_opening_hours_special}}
<div class="info-window-line info-window-opening-hours">
  <span class="label">#trans ""Special Opening Hours""#:</span>
  {{var opening_hours_special}}
</div>
{{/if}}

{{widget type="Infrangible\GoogleMaps\Block\Widget\GoogleMaps\SimpleMarker" api_key="' . $apiKey .
                    '" map_id="retail_store_id" height="500" lat="#var latitude#" lng="#var longitude#" title="#var name#" zoom="13" zoom_control="1" map_type_control="0" street_view_control="0" rotate_control="0" scale_control="0" fullscreen_control="0" administrative="1" landscape="0" poi="0" road="1" transit="1" water="1"}}'],
            [BlockInterface::IDENTIFIER => 'store_info_window',
                BlockInterface::TITLE => 'Store Info Window',
                BlockInterface::CONTENT => '<h3>{{var name}}</h3>
<div class="info-window-line info-window-address"><span class="label">#trans "Address"#:</span> {{var street}}, {{var postcode}} {{var city}}</div>
<div class="info-window-line info-window-phone"><span class="label">#trans "Phone Number"#:</span> {{var phone_number}}</div>
<div class="info-window-line info-window-mail"><span class="label">#trans "E-Mail"#:</span> {{var email}}</div>
{{if has_opening_hours_special}}
<div class="info-window-line info-window-opening-hours"><span class="label">#trans "Opening Hours"#:</span> {{var opening_hours}}</div>
{{/if}}
<div class="info-window-link"><a href="{{var url}}" class="action primary">#trans "Store Detail Page"#</a></div>']];

        $cmsBlockIds = [];

        foreach ($cmsBlockList as $cmsBlockData) {
            $cmsBlock = $this->cmsHelper->loadCmsBlockByIdentifier($cmsBlockData[BlockInterface::IDENTIFIER]);
            $cmsBlockId = $cmsBlock->getId();

            if (!$cmsBlockId) {
                $this->cmsHelper->importBlock($cmsBlockData);

                $cmsBlock = $this->cmsHelper->loadCmsBlockByIdentifier($cmsBlockData[BlockInterface::IDENTIFIER]);
                $cmsBlockId = $cmsBlock->getId();
            }

            $cmsBlockIds[$cmsBlockData[BlockInterface::IDENTIFIER]] = $cmsBlockId;
        }

        foreach ($cmsBlockList as $cmsBlockData) {
            $cmsBlock = $this->cmsHelper->loadCmsBlockByIdentifier($cmsBlockData[BlockInterface::IDENTIFIER]);
            $cmsBlockId = $cmsBlock->getId();

            if ($cmsBlockId) {
                $content = $cmsBlockData[BlockInterface::CONTENT];

                if (preg_match_all('/<<(.*?)>>/', $content, $matches)) {
                    if (array_key_exists(0, $matches) && is_array($matches[0]) && array_key_exists(1, $matches) &&
                        is_array($matches[1])) {
                        foreach ($matches[0] as $key => $replace) {
                            $cmsBlockIdentifier = $matches[1][$key];

                            if (array_key_exists($cmsBlockIdentifier, $cmsBlockIds)) {
                                $content = str_replace($replace, $cmsBlockIds[$cmsBlockIdentifier], $content);
                            }
                        }
                    }
                }

                $cmsBlock->setContent($content);

                $this->cmsHelper->saveCmsBlock($cmsBlock);
            }
        }

        $cmsPageList = [['identifier' => 'retail-stores',
            'title' => __('Retail Stores'),
            'page_layout' => '1column',
            'content' => '<div>{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="<<store_search>>"}}</div>
<div>{{widget type="Magento\Cms\Block\Widget\Block" template="widget/static_block/default.phtml" block_id="<<store_map>>"}}</div>']];

        foreach ($cmsPageList as $cmsPageData) {
            $cmsPage = $this->cmsHelper->loadCmsPageByIdentifier($cmsPageData['identifier']);
            $cmsPageId = $cmsPage->getId();

            if (!$cmsPageId) {
                $content = $cmsPageData['content'];

                if (preg_match_all('/<<(.*?)>>/', $content, $matches)) {
                    if (array_key_exists(0, $matches) && is_array($matches[0]) && array_key_exists(1, $matches) &&
                        is_array($matches[1])) {
                        foreach ($matches[0] as $key => $replace) {
                            $cmsBlockIdentifier = $matches[1][$key];

                            if (array_key_exists($cmsBlockIdentifier, $cmsBlockIds)) {
                                $content = str_replace($replace, $cmsBlockIds[$cmsBlockIdentifier], $content);
                            }
                        }
                    }
                }

                $cmsPageData['content'] = $content;

                $this->cmsHelper->importPage($cmsPageData);
            }
        }

        $this->storeHelper->insertConfigValue('infrangible_retailstore/store/cms_block_id',
            $cmsBlockIds['store_detail_page']);

        $this->storeHelper->insertConfigValue('infrangible_retailstore/store/info_window_cms_block_id',
            $cmsBlockIds['store_info_window']);

        return 0;
    }
}
