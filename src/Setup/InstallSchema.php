<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Setup;

use Infrangible\Core\Helper\Setup;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema
    implements InstallSchemaInterface
{
    /** @var Setup */
    protected $eavSetupHelper;

    public function __construct(Setup $eavSetupHelper)
    {
        $this->eavSetupHelper = $eavSetupHelper;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $this->eavSetupHelper->addEntityTypeTables($setup, 'retail_store_entity');

        $retailStoreTableName = $setup->getTable('retail_store_entity');
        $storeFeatureTableName = $setup->getTable('retail_store_feature');
        $storeLocationTableName = $setup->getTable('retail_store_location');
        $quoteTableName = $setup->getTable('quote');
        $orderTableName = $setup->getTable('sales_order');

        if (!$connection->isTableExists($storeFeatureTableName)) {
            $storeFeatureTable = $connection->newTable($storeFeatureTableName);

            $storeFeatureTable->addColumn('id', Table::TYPE_INTEGER, null, ['identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true]);
            $storeFeatureTable->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false]);
            $storeFeatureTable->addColumn('image', Table::TYPE_TEXT, null, ['nullable' => false]);
            $storeFeatureTable->addColumn('status', Table::TYPE_SMALLINT, 1);

            $connection->createTable($storeFeatureTable);
        }

        if (!$connection->isTableExists($storeLocationTableName)) {
            $retailStoreLocationTable = $connection->newTable($storeLocationTableName);

            $retailStoreLocationTable->addColumn('location_id', Table::TYPE_INTEGER, null, ['identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true], 'Search Id');
            $retailStoreLocationTable->addColumn('key', Table::TYPE_TEXT, 32, ['nullable' => false], 'Key');
            $retailStoreLocationTable->addColumn('location', Table::TYPE_TEXT, 256, ['nullable' => false], 'Location');

            $retailStoreLocationTable->addIndex($connection->getIndexName('retail_store_location', ['key']), ['key'],
                AdapterInterface::INDEX_TYPE_UNIQUE);

            $connection->createTable($retailStoreLocationTable);
        }

        if (!$connection->tableColumnExists($quoteTableName, 'retail_store_id')) {
            $connection->addColumn($quoteTableName, 'retail_store_id', ['type' => Table::TYPE_INTEGER,
                'length' => 10,
                'unsigned' => true,
                'nullable' => true,
                'default' => null,
                'comment' => 'Entity Id of Retail Store']);

            $connection->addForeignKey($setup->getFkName($quoteTableName, 'retail_store_id', $retailStoreTableName,
                'entity_id'), $quoteTableName, 'retail_store_id', $retailStoreTableName, 'entity_id',
                Table::ACTION_CASCADE);
        }

        if (!$connection->tableColumnExists($orderTableName, 'retail_store_id')) {
            $connection->addColumn($orderTableName, 'retail_store_id', ['type' => Table::TYPE_INTEGER,
                'length' => 10,
                'unsigned' => true,
                'nullable' => true,
                'default' => null,
                'comment' => 'Entity Id of Retail Store']);

            $connection->addForeignKey($setup->getFkName($orderTableName, 'retail_store_id', $retailStoreTableName,
                'entity_id'), $orderTableName, 'retail_store_id', $retailStoreTableName, 'entity_id',
                Table::ACTION_CASCADE);
        }

        $setup->endSetup();
    }
}
