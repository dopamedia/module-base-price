<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 06.01.17
 */

namespace Dopamedia\BasePrice\Plugin\Pricing;

class AroundRendererPlugin
{
    const BLOCK_NAME_BASE_PRICE = 'baseprice.product.price.base_price';

    /**
     * @param \Magento\Framework\Pricing\Render $subject
     * @param \Closure $proceed
     * @param string $priceCode
     * @param \Magento\Framework\Pricing\SaleableInterface $saleableItem
     * @param array $arguments
     * @return string
     */
    public function aroundRender(
        \Magento\Framework\Pricing\Render $subject,
        \Closure $proceed,
        $priceCode,
        \Magento\Framework\Pricing\SaleableInterface $saleableItem,
        array $arguments = []
    ) {
        $result = $proceed($priceCode, $saleableItem, $arguments);
        if (trim($result) !== '') {
            /** @var \Dopamedia\BasePrice\Block\Product\Price\BasePrice $block */
            $block = $subject->getLayout()->getBlock(self::BLOCK_NAME_BASE_PRICE);
            if ($block) {
                $result = $result . $block->setSaleableItem($saleableItem)->toHtml();
            }
        }
        return $result;
    }
}