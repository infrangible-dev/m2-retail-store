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
use Infrangible\RetailStore\Model\ResourceModel\Store\CollectionFactory;
use Infrangible\RetailStore\Model\StoreFactory;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class RetailStoreDemo
    extends Script
{
    /** @var StoreFactory */
    protected $retailStoreFactory;

    /** @var \Infrangible\RetailStore\Model\ResourceModel\StoreFactory */
    protected $retailStoreResourceFactory;

    /** @var CollectionFactory */
    protected $retailStoreCollectionFactory;

    /** @var Cms */
    protected $cmsHelper;

    /** @var Stores */
    protected $storeHelper;

    public function __construct(
        StoreFactory $retailStoreFactory,
        \Infrangible\RetailStore\Model\ResourceModel\StoreFactory $retailStoreResourceFactory,
        CollectionFactory $retailStoreCollectionFactory,
        Cms $cmsHelper,
        Stores $storeHelper)
    {
        $this->retailStoreFactory = $retailStoreFactory;
        $this->retailStoreResourceFactory = $retailStoreResourceFactory;
        $this->retailStoreCollectionFactory = $retailStoreCollectionFactory;
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
        $retailStoreResource = $this->retailStoreResourceFactory->create();

        $retailStoreList = [['code' => 'munich',
            'status' => 1,
            'name' => 'München',
            'url_key' => 'munich',
            'email' => 'munich@retail.store',
            'phone_number' => '089 69931222',
            'street' => 'Werner-Heisenberg-Allee 25',
            'postcode' => '80939',
            'city' => 'München',
            'country' => 'DE',
            'latitude' => '48.2190',
            'longitude' => '11.6247'],
            ['code' => 'dortmund',
                'status' => 1,
                'name' => 'Dortmund',
                'url_key' => 'dortmund',
                'email' => 'dortmund@retail.store',
                'phone_number' => '0231 90200',
                'street' => 'Strobelallee 50',
                'postcode' => '44139',
                'city' => 'Dortmund',
                'country' => 'DE',
                'latitude' => '51.4927',
                'longitude' => '7.4518'],
            ['code' => 'leipzig',
                'status' => 1,
                'name' => 'Leipzig',
                'url_key' => 'leipzig',
                'email' => 'leipzig@retail.store',
                'phone_number' => '0341 124797777',
                'street' => 'Am Sportforum 3',
                'postcode' => '04105',
                'city' => 'Leipzig',
                'country' => 'DE',
                'latitude' => '51.3459',
                'longitude' => '12.3483'],
            ['code' => 'union',
                'status' => 1,
                'name' => 'Union',
                'url_key' => 'union',
                'email' => 'union@retail.store',
                'phone_number' => '030 6566880',
                'street' => 'An d. Wuhlheide 263',
                'postcode' => '12555',
                'city' => 'Berlin',
                'country' => 'DE',
                'latitude' => '52.4575',
                'longitude' => '13.5681'],
            ['code' => 'freiburg',
                'status' => 1,
                'name' => 'Freiburg',
                'url_key' => 'freiburg',
                'email' => 'freiburg@retail.store',
                'phone_number' => '0761 385510',
                'street' => 'Achim-Stocker-Straße 1',
                'postcode' => '79108',
                'city' => 'Freiburg im Breisgau',
                'country' => 'DE',
                'latitude' => '48.0212',
                'longitude' => '7.8291'],
            ['code' => 'leverkusen',
                'status' => 1,
                'name' => 'Leverkusen',
                'url_key' => 'leverkusen',
                'email' => 'leverkusen@retail.store',
                'phone_number' => '0214 50001904',
                'street' => 'Bismarckstraße 122-124',
                'postcode' => '51373',
                'city' => 'Leverkusen',
                'country' => 'DE',
                'latitude' => '51.0383',
                'longitude' => '7.0022'],
            ['code' => 'frankfurt',
                'status' => 1,
                'name' => 'Frankfurt',
                'url_key' => 'frankfurt',
                'email' => 'frankfurt@retail.store',
                'phone_number' => '069 955031585',
                'street' => 'Mörfelder Landstraße 362',
                'postcode' => '60528',
                'city' => 'Frankfurt am Main',
                'country' => 'DE',
                'latitude' => '50.0687',
                'longitude' => '8.6454'],
            ['code' => 'wolfsburg',
                'status' => 1,
                'name' => 'Wolfsburg',
                'url_key' => 'wolfsburg',
                'email' => 'wolfsburg@retail.store',
                'phone_number' => '05361 8903903',
                'street' => 'In den Allerwiesen 1',
                'postcode' => '38446',
                'city' => 'Wolfsburg',
                'country' => 'DE',
                'latitude' => '52.4328',
                'longitude' => '10.8038'],
            ['code' => 'mainz',
                'status' => 1,
                'name' => 'Mainz',
                'url_key' => 'mainz',
                'email' => 'mainz@retail.store',
                'phone_number' => '06131 375500',
                'street' => 'Eugen-Salomon-Straße 1',
                'postcode' => '55128',
                'city' => 'Mainz',
                'country' => 'DE',
                'latitude' => '49.9841',
                'longitude' => '8.2243'],
            ['code' => 'mönchengladbach',
                'status' => 1,
                'name' => 'Mönchengladbach',
                'url_key' => 'mönchengladbach',
                'email' => 'mönchengladbach@retail.store',
                'phone_number' => '0180 6 181900',
                'street' => 'Hennes Weisweiler Allee 1',
                'postcode' => '41179',
                'city' => 'Mönchengladbach',
                'country' => 'DE',
                'latitude' => '51.1747',
                'longitude' => '6.3854'],
            ['code' => 'köln',
                'status' => 1,
                'name' => 'Köln',
                'url_key' => 'köln',
                'email' => 'köln@retail.store',
                'phone_number' => '0221 71616150',
                'street' => 'Aachener Str. 999',
                'postcode' => '50933',
                'city' => 'Köln',
                'country' => 'DE',
                'latitude' => '50.9336',
                'longitude' => '6.8751'],
            ['code' => 'hoffenheim',
                'status' => 1,
                'name' => 'Hoffenheim',
                'url_key' => 'hoffenheim',
                'email' => 'hoffenheim@retail.store',
                'phone_number' => '07261 94930',
                'street' => 'Dietmar-Hopp-Straße 1',
                'postcode' => '74889',
                'city' => 'Sinsheim',
                'country' => 'DE',
                'latitude' => '49.2381',
                'longitude' => '8.8879'],
            ['code' => 'bremen',
                'status' => 1,
                'name' => 'Bremen',
                'url_key' => 'bremen',
                'email' => 'bremen@retail.store',
                'phone_number' => '0421 696360',
                'street' => 'Franz-Böhmert-Straße 1',
                'postcode' => '28205',
                'city' => 'Bremen',
                'country' => 'DE',
                'latitude' => '53.0669',
                'longitude' => '8.8379'],
            ['code' => 'bochum',
                'status' => 1,
                'name' => 'Bochum',
                'url_key' => 'bochum',
                'email' => 'bochum@retail.store',
                'phone_number' => '0234 951848',
                'street' => 'Castroper Str. 145',
                'postcode' => '44791',
                'city' => 'Bochum',
                'country' => 'DE',
                'latitude' => '51.4900',
                'longitude' => '7.2365'],
            ['code' => 'augsburg',
                'status' => 1,
                'name' => 'Augsburg',
                'url_key' => 'augsburg',
                'email' => 'augsburg@retail.store',
                'phone_number' => '0821 650400',
                'street' => 'Bürgermeister-Ulrich-Straße 90',
                'postcode' => '86199',
                'city' => 'Augsburg',
                'country' => 'DE',
                'latitude' => '48.3233',
                'longitude' => '10.8864'],
            ['code' => 'stuttgart',
                'status' => 1,
                'name' => 'stuttgart',
                'url_key' => 'stuttgart',
                'email' => 'stuttgart@retail.store',
                'phone_number' => '0711 99331893',
                'street' => 'Mercedesstraße 87',
                'postcode' => '70372',
                'city' => 'Stuttgart',
                'country' => 'DE',
                'latitude' => '48.7924',
                'longitude' => '9.2320'],
            ['code' => 'schalke',
                'status' => 1,
                'name' => 'Schalke',
                'url_key' => 'schalke',
                'email' => 'schalke@retail.store',
                'phone_number' => '0209 36180',
                'street' => 'Rudi-Assauer-Platz 1',
                'postcode' => '45891',
                'city' => 'Gelsenkirchen',
                'country' => 'DE',
                'latitude' => '51.5547',
                'longitude' => '7.0675'],
            ['code' => 'hertha',
                'status' => 1,
                'name' => 'Hertha',
                'url_key' => 'hertha',
                'email' => 'hertha@retail.store',
                'phone_number' => '030 30688100',
                'street' => 'Olympischer Platz 3',
                'postcode' => '14053',
                'city' => 'Berlin',
                'country' => 'DE',
                'latitude' => '52.5148',
                'longitude' => '13.2395'],];

        foreach ($retailStoreList as $retailStoreData) {
            $retailStoreCollection = $this->retailStoreCollectionFactory->create();
            $retailStoreCollection->addAttributeToFilter('code', $retailStoreData['code']);

            if ($retailStoreCollection->getSize() > 0) {
                continue;
            }

            $retailStore = $this->retailStoreFactory->create();

            $retailStore->setData($retailStoreData);

            $retailStoreResource->save($retailStore);
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

{{widget type="Infrangible\GoogleMaps\Block\Widget\GoogleMaps\SimpleMarker" api_key="AIzaSyDIML_isB24WGQ6GBpK9RSmkH5MUp_Dqt8" map_id="retail_store_id" height="500" lat="#var latitude#" lng="#var longitude#" title="#var name#" zoom="13" zoom_control="1" map_type_control="0" street_view_control="0" rotate_control="0" scale_control="0" fullscreen_control="0" administrative="1" landscape="0" poi="0" road="1" transit="1" water="1"}}']];

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

        return 0;
    }
}
