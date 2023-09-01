<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 24.05.18
 * Time: 11:04
 */

namespace Dopamedia\BasePrice\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;
use Dopamedia\BasePrice\Api\Data\ProductAttributeInterface as BasePriceProductAttributeInterface;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var EavSetup
     */
    protected $eavSetup;

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
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->changeAttributeBackendTypesToDecimal();
        }

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $this->changeAttributesToUserDefined();
        }
    }

    /**
     * change the backend types to decimal for the specified attributes
     *
     */
    private function changeAttributeBackendTypesToDecimal()
    {
        $attributesToUpdate = [BasePriceProductAttributeInterface::CODE_BASE_PRICE_PRODUCT_AMOUNT,
                               BasePriceProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_AMOUNT];

        $eavSetup = $this->eavSetup;

        foreach ($attributesToUpdate as $attribute_code) {
            $eavSetup->updateAttribute(Product::ENTITY, $attribute_code, 'backend_type', 'decimal');
            $this->moveData($eavSetup->getAttribute(Product::ENTITY, $attribute_code), 'int', 'decimal');
        }
    }
    
    private function changeAttributesToUserDefined()
    {
        $attributesToUpdate = [
            BasePriceProductAttributeInterface::CODE_ENABLE_BASE_PRICE,
            BasePriceProductAttributeInterface::CODE_BASE_PRICE_PRODUCT_AMOUNT,
            BasePriceProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_AMOUNT,
            BasePriceProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_UNIT,
            BasePriceProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_AMOUNT
        ];

        $eavSetup = $this->eavSetup;

        foreach ($attributesToUpdate as $attribute_code) {
            $eavSetup->updateAttribute(Product::ENTITY, $attribute_code, 'is_user_defined', true);
        }
    }

    private function moveData($attribute, $from, $to)
    {
        $setup = $this->eavSetup->getSetup();
        $setup->startSetup();
        $connection = $setup->getConnection();
        $originTable = $connection->getTableName('catalog_product_entity_' . $from);
        $destinationTable = $connection->getTableName('catalog_product_entity_' . $to);
        $attributeId = $attribute['attribute_id'];


        // copy data to the new table
        $select = $connection
            ->select()
            ->from($originTable)
            ->where(
            'attribute_id = ?', $attributeId
        );

        $insertQuery = $connection->insertFromSelect($select, $destinationTable);
        $connection->query($insertQuery);

        // remove data from the old table
        $connection->delete($originTable, ['attribute_id=?' => $attributeId]);
        $setup->endSetup();
    }
}