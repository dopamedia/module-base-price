<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 06.01.17
 */

namespace Dopamedia\BasePrice\Block\Product\Price;

use Dopamedia\BasePrice\Api\Data\ProductAttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Template;

class BasePrice extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Dopamedia\BasePrice\Helper\Data
     */
    private $basePriceHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $pricingHelper;

    /**
     * @var \Magento\Framework\Pricing\SaleableInterface|\Magento\Catalog\Model\Product
     */
    private $saleableItem;

    /**
     * @param Template\Context $context
     * @param \Dopamedia\BasePrice\Helper\Data $basePriceHelper
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Dopamedia\BasePrice\Helper\Data $basePriceHelper,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        $this->basePriceHelper = $basePriceHelper;
        $this->pricingHelper = $pricingHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Pricing\SaleableInterface $saleableItem
     * @return BasePrice
     */
    public function setSaleableItem(\Magento\Framework\Pricing\SaleableInterface $saleableItem)
    {
        $this->saleableItem = $saleableItem;
        return $this;
    }

    /**
     * @return float|string
     */
    public function getBasePrice()
    {
        $basePrice = $this->basePriceHelper->calculateBasePrice($this->saleableItem);
        return $this->pricingHelper->currency($basePrice);
    }

    /**
     * @return string
     */
    public function getReferenceAmount()
    {
        return round($this->saleableItem->getData(ProductAttributeInterface::CODE_BASE_PRICE_REFERENCE_AMOUNT));
    }

    /**
     * @return string
     */
    public function getReferenceUnitName()
    {
        return $this->basePriceHelper->getReferenceUnit($this->saleableItem)->getName();
    }

    /**
     * @return string
     */
    public function getReferenceUnitSymbol()
    {
        return $this->basePriceHelper->getReferenceUnit($this->saleableItem)->getSymbol();
    }

    /**
     * @inheritDoc
     */
    protected function _beforeToHtml()
    {
        if ($this->saleableItem === null or !$this->saleableItem->getId()) {
            throw new LocalizedException(
                new Phrase('SaleableItem is not set or could not be loaded')
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        if (!$this->saleableItem->getData(ProductAttributeInterface::CODE_ENABLE_BASE_PRICE)) {
            return '';
        }
        return parent::_toHtml();
    }
}