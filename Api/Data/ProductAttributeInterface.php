<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 06.01.17
 */

namespace Dopamedia\BasePrice\Api\Data;

interface ProductAttributeInterface extends \Magento\Catalog\Api\Data\ProductAttributeInterface
{
    const CODE_ENABLE_BASE_PRICE = 'enable_base_price';
    const CODE_BASE_PRICE_PRODUCT_UNIT = 'base_price_product_unit';
    const CODE_BASE_PRICE_PRODUCT_AMOUNT = 'base_price_product_amount';
    const CODE_BASE_PRICE_REFERENCE_UNIT = 'base_price_reference_unit';
    const CODE_BASE_PRICE_REFERENCE_AMOUNT = 'base_price_reference_amount';
}