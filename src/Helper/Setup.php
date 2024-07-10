<?php

declare(strict_types=1);

namespace Infrangible\RetailStore\Helper;

use Exception;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\SetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Setup
{
    /** @var EavSetupFactory */
    protected $eavSetupFactory;

    /** @var \Infrangible\Core\Helper\Setup */
    protected $setupHelper;

    public function __construct(EavSetupFactory $eavSetupFactory, \Infrangible\Core\Helper\Setup $setupHelper)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->setupHelper = $setupHelper;
    }

    /**
     * @param SetupInterface $setup
     * @param string $attributeCode
     * @param string $label
     * @param string $type
     * @param string $input
     * @param string|null $default
     * @param int $sortOrder
     * @param bool $userDefined
     * @param bool $required
     * @param bool $visible
     * @param bool $visibleOnFront
     * @param string|null $backendModel
     * @param string|null $sourceModel
     *
     * @throws Exception
     */
    public function addAttribute(
        SetupInterface $setup,
        string $attributeCode,
        string $label,
        string $type,
        string $input,
        ?string $default = null,
        int $sortOrder = 10,
        bool $userDefined = false,
        bool $required = false,
        bool $visible = true,
        bool $visibleOnFront = false,
        string $backendModel = null,
        string $sourceModel = null)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $this->setupHelper->addEavEntityAttribute($eavSetup, 'retail_store', $attributeCode, $label,
            ScopedAttributeInterface::SCOPE_GLOBAL, $type, $input, $sortOrder, $default, $userDefined, $required, false,
            false, false, false, $visible, $visibleOnFront, false, $backendModel, $sourceModel);

        $this->setupHelper->addAttributeToSetAndGroup($eavSetup, 'retail_store', $attributeCode, 'Default', 'Default');
    }
}
