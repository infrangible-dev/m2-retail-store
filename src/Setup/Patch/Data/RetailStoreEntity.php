<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\RetailStore\Setup\Patch\Data;

use Infrangible\Core\Helper\Setup;
use Infrangible\RetailStore\Model\Config\Source\RetailStoreNoteType;
use Infrangible\RetailStore\Model\Entity\Attribute\Source\ClickAndCollectType;
use Infrangible\RetailStore\Model\Entity\Attribute\Source\CmsBlock;
use Infrangible\RetailStore\Model\Entity\Attribute\Source\Feature;
use Infrangible\RetailStore\Model\Store;
use Magento\Customer\Model\ResourceModel\Address\Attribute\Source\Country;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Validator\ValidateException;

/**
 * Adds the EAV entity type "retail_store" with its attribute set and attributes.
 *
 * Installations of version 1 already have them; the patch stores the note type as text instead of a number, which
 * lost the selected type.
 */
class RetailStoreEntity implements DataPatchInterface
{
    /** Name of the attribute set and group. */
    public const ATTRIBUTE_SET = 'Default';

    /**
     * The attributes: code => label, type, input, sort order, required, visible, visible on front, default, backend
     * model, source model.
     */
    private const ATTRIBUTES = [
        'code'                   => ['Retail Store Code', 'varchar', 'text', 10, true, true, false],
        'status'                 => ['Retail Store Status', 'int', 'text', 20, true, true, false, '1'],
        'name'                   => ['Retail Store Name', 'varchar', 'text', 30, true, true, true],
        'description'            => ['Retail Store Description', 'text', 'textarea', 40, false, true, true],
        'direction'              => ['Retail Store Direction', 'text', 'textarea', 50, false, true, true],
        'email'                  => ['Retail Store E-Mail', 'varchar', 'text', 60, true, true, true],
        'phone_number'           => ['Retail Store Phone Number', 'varchar', 'text', 70, false, true, true],
        'fax_number'             => ['Retail Store Fax Number', 'varchar', 'text', 80, false, true, true],
        'opening_hours'          => ['Retail Store Opening Hours', 'text', 'textarea', 90, false, true, true],
        'opening_hours_special'  => ['Retail Store Special Opening Hours', 'text', 'textarea', 100, false, true, true],
        'image'                  => ['Retail Store Image', 'text', 'text', 110, false, true, true],
        'street'                 => ['Retail Store Street', 'varchar', 'text', 120, true, true, true],
        'postcode'               => ['Retail Store Postcode', 'varchar', 'text', 130, true, true, true],
        'city'                   => ['Retail Store City', 'varchar', 'text', 140, true, true, true],
        'country'                => [
            'Retail Store Country', 'varchar', 'select', 150, true, true, true, null, null, Country::class
        ],
        'latitude'               => ['Retail Store Latitude', 'decimal', 'text', 160, true, true, false],
        'longitude'              => ['Retail Store Longitude', 'decimal', 'text', 170, true, true, false],
        'map_image'              => ['Retail Store Map Image', 'text', 'textarea', 180, false, true, true],
        'feature'                => [
            'Retail Store Feature', 'text', 'multiselect', 190, true, true, true, null, ArrayBackend::class,
            Feature::class
        ],
        'click_and_collect_type' => [
            'Retail Store Click & Collect Service Type', 'int', 'select', 200, true, true, true, null, null,
            ClickAndCollectType::class
        ],
        'pickup_point_desc'      => [
            'Retail Store Pickup Point Description', 'text', 'textarea', 210, false, true, true
        ],
        'url_key'                => ['URL Key', 'varchar', 'text', 220, true, true, false],
        'seo_text'               => ['SEO Text', 'text', 'text', 230, true, true, false],
        'cms_block_id'           => ['CMS Block', 'int', 'select', 240, true, true, false, null, null, CmsBlock::class],
        'note'                   => ['Retail Store Note', 'varchar', 'text', 300, false, true, false],
        'note_type'              => [
            'Retail Store Note Type', 'varchar', 'select', 301, false, true, false, null, null,
            RetailStoreNoteType::class
        ]
    ];

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Setup $setupHelper
     */
    public function __construct(
        protected ModuleDataSetupInterface $moduleDataSetup,
        protected Setup $setupHelper
    ) {
    }

    /**
     * Adds the entity type, the attribute set and the attributes.
     *
     * @return $this
     * @throws LocalizedException
     * @throws ValidateException
     */
    public function apply(): static
    {
        $connection = $this->moduleDataSetup->getConnection();

        $connection->startSetup();

        $eavSetup = $this->setupHelper->getEavSetup($this->moduleDataSetup);

        $this->setupHelper->addEntityType($eavSetup, Store::ENTITY, Store::class, null, null, 'retail_store_entity');

        $entityTypeId = (int)$eavSetup->getEntityTypeId(Store::ENTITY);

        $eavSetup->addAttributeSet($entityTypeId, self::ATTRIBUTE_SET);

        $attributeSetId = (int)$eavSetup->getAttributeSetId($entityTypeId, self::ATTRIBUTE_SET);

        $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, self::ATTRIBUTE_SET);
        $eavSetup->setDefaultSetToEntityType($entityTypeId, self::ATTRIBUTE_SET);

        $this->convertNoteType($eavSetup, $entityTypeId);

        foreach (self::ATTRIBUTES as $attributeCode => $attribute) {
            $this->setupHelper->addEavEntityAttribute(
                $eavSetup,
                Store::ENTITY,
                $attributeCode,
                $attribute[ 0 ],
                ScopedAttributeInterface::SCOPE_GLOBAL,
                $attribute[ 1 ],
                $attribute[ 2 ],
                $attribute[ 3 ],
                $attribute[ 7 ] ?? null,
                false,
                $attribute[ 4 ],
                false,
                false,
                false,
                false,
                $attribute[ 5 ],
                $attribute[ 6 ],
                false,
                $attribute[ 8 ] ?? null,
                $attribute[ 9 ] ?? null
            );

            $this->setupHelper->addAttributeToSetAndGroup(
                $eavSetup,
                Store::ENTITY,
                $attributeCode,
                self::ATTRIBUTE_SET,
                self::ATTRIBUTE_SET
            );
        }

        $connection->endSetup();

        return $this;
    }

    /**
     * Stores the note type as text: version 1 stored it as number, so every type was saved as 0, which is removed.
     *
     * @param EavSetup $eavSetup
     * @param int $entityTypeId
     *
     * @return void
     */
    protected function convertNoteType(EavSetup $eavSetup, int $entityTypeId): void
    {
        $attributeId = $eavSetup->getAttributeId($entityTypeId, 'note_type');

        if (! $attributeId || $eavSetup->getAttribute($entityTypeId, 'note_type', 'backend_type') !== 'int') {
            return;
        }

        $this->moduleDataSetup->getConnection()->delete(
            $this->moduleDataSetup->getTable('retail_store_entity_int'),
            ['attribute_id = ?' => $attributeId]
        );

        $eavSetup->updateAttribute($entityTypeId, 'note_type', 'backend_type', 'varchar');
    }

    /**
     * The patch has no dependencies.
     *
     * @return string[]
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * The patch has no aliases.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return [];
    }
}
