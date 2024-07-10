<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Setup;

use FeWeDev\Base\Arrays;
use Infrangible\Core\Helper\Setup;
use Infrangible\RetailStore\Model\Config\Source\RetailStoreNoteType;
use Infrangible\RetailStore\Model\Entity\Attribute\Source\ClickAndCollectType;
use Infrangible\RetailStore\Model\Entity\Attribute\Source\CmsBlock;
use Infrangible\RetailStore\Model\Entity\Attribute\Source\Feature;
use Infrangible\RetailStore\Model\Store;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Country;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallData
    implements InstallDataInterface
{
    /** @var Arrays */
    protected $arrayHelper;

    /** @var Setup */
    protected $eavSetupHelper;

    /** @var \Infrangible\RetailStore\Helper\Setup */
    protected $retailStoreSetupHelper;

    /** @var EavSetupFactory */
    protected $eavSetupFactory;

    public function __construct(
        Arrays $arrays,
        Setup $eavSetupHelper,
        \Infrangible\RetailStore\Helper\Setup $retailStoreSetupHelper,
        EavSetupFactory $eavSetupFactory)
    {
        $this->arrayHelper = $arrays;
        $this->eavSetupHelper = $eavSetupHelper;
        $this->retailStoreSetupHelper = $retailStoreSetupHelper;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @throws \Exception
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $this->installRetailStore($eavSetup, $setup);
        $this->installRetailStore($eavSetup, $setup);

        $setup->endSetup();
    }

    /**
     * @throws \Exception
     */
    private function installRetailStore(EavSetup $eavSetup, ModuleDataSetupInterface $setup): void
    {
        $retailStoreEntityTypeId = $this->addEntityType($eavSetup);

        $this->addAttributeSet($eavSetup, $retailStoreEntityTypeId);

        $this->addEntityAttributes($setup);
    }

    private function addEntityType(EavSetup $eavSetup): int
    {
        $this->eavSetupHelper->addEntityType($eavSetup, 'retail_store', Store::class, null, null,
            'retail_store_entity');

        /** @var array $retailStoreEntityTypeData */
        $retailStoreEntityTypeData = $eavSetup->getEntityType('retail_store');

        return (int)$this->arrayHelper->getValue($retailStoreEntityTypeData, 'entity_type_id');
    }

    private function addAttributeSet(EavSetup $eavSetup, int $retailStoreEntityTypeId)
    {
        $eavSetup->addAttributeSet($retailStoreEntityTypeId, 'Default');

        /** @var array $retailStoreAttributeSetData */
        $retailStoreAttributeSetData = $eavSetup->getAttributeSet($retailStoreEntityTypeId, 'Default');

        $retailStoreAttributeSetId = $this->arrayHelper->getValue($retailStoreAttributeSetData, 'attribute_set_id');

        $eavSetup->addAttributeGroup($retailStoreEntityTypeId, $retailStoreAttributeSetId, 'Default');

        $eavSetup->setDefaultSetToEntityType($retailStoreEntityTypeId, $retailStoreAttributeSetId);
    }

    /**
     * @throws \Exception
     */
    private function addEntityAttributes(ModuleDataSetupInterface $setup)
    {
        $this->retailStoreSetupHelper->addAttribute($setup, 'code', 'Retail Store Code', 'varchar', 'text', null, 10,
            false, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'status', 'Retail Store Status', 'int', 'text', '1', 20,
            false, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'name', 'Retail Store Name', 'varchar', 'text', null, 30,
            false, true, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'description', 'Retail Store Description', 'text',
            'textarea', null, 40, false, false, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'direction', 'Retail Store Direction', 'text', 'textarea',
            null, 50, false, false, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'email', 'Retail Store E-Mail', 'varchar', 'text', null, 60,
            false, true, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'phone_number', 'Retail Store Phone Number', 'varchar',
            'text', null, 70, false, false, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'fax_number', 'Retail Store Fax Number', 'varchar', 'text',
            null, 80, false, false, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'opening_hours', 'Retail Store Opening Hours', 'text',
            'textarea', null, 90, false, false, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'opening_hours_special',
            'Retail Store Special Opening Hours', 'text', 'textarea', null, 100, false, false, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'image', 'Retail Store Image', 'text', 'text', null, 110,
            false, false, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'street', 'Retail Store Street', 'varchar', 'text', null,
            120, false, true, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'postcode', 'Retail Store Postcode', 'varchar', 'text',
            null, 130, false, true, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'city', 'Retail Store City', 'varchar', 'text', null, 140,
            false, true, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'country', 'Retail Store Country', 'varchar', 'select',
            null, 150, false, true, true, true, null, Country::class);
        $this->retailStoreSetupHelper->addAttribute($setup, 'latitude', 'Retail Store Latitude', 'decimal', 'text',
            null, 160, false, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'longitude', 'Retail Store Longitude', 'decimal', 'text',
            null, 170, false, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'map_image', 'Retail Store Map Image', 'text', 'textarea',
            null, 180, false, false, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'feature', 'Retail Store Feature', 'text', 'multiselect',
            null, 190, false, true, true, true, ArrayBackend::class, Feature::class);
        $this->retailStoreSetupHelper->addAttribute($setup, 'click_and_collect_type',
            'Retail Store Click & Collect Service Type', 'int', 'select', null, 200, false, true, true, true, null,
            ClickAndCollectType::class);
        $this->retailStoreSetupHelper->addAttribute($setup, 'pickup_point_desc',
            'Retail Store Pickup Point Description', 'text', 'textarea', null, 210, false, false, true, true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'url_key', 'URL Key', 'varchar', 'text', null, 220, false,
            true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'seo_text', 'SEO Text', 'text', 'text', null, 230, false,
            true);
        $this->retailStoreSetupHelper->addAttribute($setup, 'cms_block_id', 'CMS Block', 'int', 'select', null, 240,
            false, true, true, false, null, CmsBlock::class);
        $this->retailStoreSetupHelper->addAttribute($setup, 'note', 'Retail Store Note', 'varchar', 'text', null, 300);
        $this->retailStoreSetupHelper->addAttribute($setup, 'note_type', 'Retail Store Note Type', 'int', 'select',
            null, 301, false, false, true, false, null, RetailStoreNoteType::class);
    }
}
