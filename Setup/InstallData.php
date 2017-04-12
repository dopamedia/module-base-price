<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 06.01.17
 */

namespace Dopamedia\BasePrice\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;
use Dopamedia\BasePrice\Api\Data\ProductAttributeInterface as BasePriceProductAttributeInterface;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD)
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            BasePriceProductAttributeInterface::CODE_ENABLE_BASE_PRICE,
            [
                'type' => 'int',
                'label' => 'Enable Base Price',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'sort_order' => 10,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'used_in_product_listing' => true,
                'apply_to' => 'simple,configurable',
                'group' => 'Base Price',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'required' => false,
                'user_defined' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            BasePriceProductAttributeInterface::CODE_BASE_PRICE_PRODUCT_UNIT,
            [
                'type' => 'text',
                'label' => 'Product Unit',
                'input' => 'select',
                'source' => \Dopamedia\BasePrice\Model\Entity\Attribute\Source\Unit::class,
                'sort_order' => 20,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'used_in_product_listing' => true,
                'apply_to' => 'simple,configurable',
                'group' => 'Base Price',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'required' => false,
                'user_defined' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            BasePriceProductAttributeInterface::CODE_BASE_PRICE_PRODUCT_AMOUNT,
            [
                'type' => 'int',
                'label' => 'Product Amount',
                'input' => 'text',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'sort_order' => 30,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'used_in_product_listing' => true,
                'apply_to' => 'simple,configurable',
                'group' => 'Base Price',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'required' => false,
                'user_defined' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            BasePriceProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_UNIT,
            [
                'type' => 'text',
                'label' => 'Reference Unit',
                'input' => 'select',
                'source' => \Dopamedia\BasePrice\Model\Entity\Attribute\Source\Unit::class,
                'sort_order' => 40,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'used_in_product_listing' => true,
                'apply_to' => 'simple,configurable',
                'group' => 'Base Price',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'required' => false,
                'user_defined' => false
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            BasePriceProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_AMOUNT,
            [
                'type' => 'int',
                'label' => 'Reference Amount',
                'input' => 'text',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'used_in_product_listing' => true,
                'apply_to' => 'simple,configurable',
                'group' => 'Base Price',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'required' => false,
                'user_defined' => false
            ]
        );
    }
}