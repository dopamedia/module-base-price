<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 06.01.17
 */

namespace Dopamedia\BasePrice\Helper;


use Dopamedia\BasePrice\Api\Data\ProductAttributeInterface;
use Dopamedia\Measure\Api\Data\UnitInterface;
use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\Url\Helper\Data
{
    /**
     * @var \Dopamedia\Measure\Model\UnitConverter
     */
    private $unitConverter;

    /**
     * @var \Dopamedia\Measure\Model\BuilderInterface
     */
    private $measureBuilder;

    /**
     * @param Context $context
     * @param \Dopamedia\Measure\Model\UnitConverterInterface $unitConverter
     * @param \Dopamedia\Measure\Model\BuilderInterface $measureBuilder
     */
    public function __construct(
        Context $context,
        \Dopamedia\Measure\Model\UnitConverterInterface $unitConverter,
        \Dopamedia\Measure\Model\BuilderInterface $measureBuilder
    ) {
        $this->unitConverter = $unitConverter;
        $this->measureBuilder = $measureBuilder;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product $saleableItem
     * @return float
     */
    protected function getConvertedAmount(\Magento\Framework\Pricing\SaleableInterface $saleableItem)
    {
        return $this->unitConverter->convert(
            $this->getProductUnit($saleableItem)->getCode(),
            $this->getReferenceUnit($saleableItem)->getCode(),
            $saleableItem->getData(ProductAttributeInterface::CODE_BASE_PRICE_PRODUCT_AMOUNT)
        );
    }

    /**
     * @param \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product $saleableItem
     * @param string $attributeCode
     * @return string
     */
    private function getUnitFromAttributeText(
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        string $attributeCode
    ): string
    {
        $unit = $saleableItem->getAttributeText($attributeCode);
        return strtoupper(str_replace(' ', '_', $unit));
    }

    /**
     * @param \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product $saleableItem
     * @return float
     */
    public function calculateBasePrice(\Magento\Framework\Pricing\SaleableInterface $saleableItem)
    {
        $referenceAmount = $saleableItem->getData(ProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_AMOUNT);
        $convertedAmount = $this->getConvertedAmount($saleableItem);
        return (float) $saleableItem->getFinalPrice() / $convertedAmount * $referenceAmount;
    }

    /**
     * @param \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product $saleableItem
     * @return UnitInterface
     */
    public function getProductUnit(\Magento\Framework\Pricing\SaleableInterface $saleableItem)
    {
        $productUnitCode = $saleableItem->getData(ProductAttributeInterface::CODE_BASE_PRICE_PRODUCT_UNIT);

        if (!$this->unitConverter->checkIfUnitExists($productUnitCode)) {
            $productUnitCode = $this->getUnitFromAttributeText(
                $saleableItem, ProductAttributeInterface::CODE_BASE_PRICE_PRODUCT_UNIT
            );
        }

        return $this->measureBuilder->getUnit($productUnitCode);
    }


    /**
     * @param \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product $saleableItem
     * @return UnitInterface
     */
    public function getReferenceUnit(\Magento\Framework\Pricing\SaleableInterface $saleableItem)
    {
        $referenceUnitCode = $saleableItem->getData(ProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_UNIT);

        if (!$this->unitConverter->checkIfUnitExists($referenceUnitCode)) {
            $referenceUnitCode = $this->getUnitFromAttributeText(
                $saleableItem, ProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_UNIT
            );
        }

        return $this->measureBuilder->getUnit($referenceUnitCode);
    }
}