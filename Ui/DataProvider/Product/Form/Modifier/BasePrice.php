<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 06.01.17
 */

namespace Dopamedia\BasePrice\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Dopamedia\BasePrice\Api\Data\ProductAttributeInterface as BasePriceProductAttributeInterface;

class BasePrice extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var string
     */
    protected $scopeName;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @inheritDoc
     */
    public function __construct(ArrayManager $arrayManager, $scopeName = '')
    {
        $this->arrayManager = $arrayManager;
        $this->scopeName = $scopeName;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        if (isset($this->meta['base-price'])) {
            $this->addBasePriceLink();
            $this->addBasePriceModal();
        }
        return $this->meta;
    }

    /**
     * @return BasePrice
     */
    protected function addBasePriceLink()
    {
        $pricePath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_PRICE,
            $this->meta,
            null,
            'children'
        );

        if ($pricePath) {
            $this->meta = $this->arrayManager->merge(
                $pricePath . '/arguments/data/config',
                $this->meta,
                ['additionalClasses' => 'admin__field-small']
            );

            $basePriceButton['arguments']['data']['config'] = [
                'displayAsLink' => true,
                'formElement' => Container::NAME,
                'componentType' => Container::NAME,
                'component' => 'Magento_Ui/js/form/components/button',
                'template' => 'ui/form/components/button/container',
                'actions' => [
                    [
                        'targetName' => $this->scopeName . '.base_price_modal',
                        'actionName' => 'toggleModal',
                    ]
                ],
                'title' => __('Base Price'),
                'additionalForGroup' => true,
                'provider' => false,
                'source' => 'product_details',
                'sortOrder' =>
                    $this->arrayManager->get($pricePath . '/arguments/data/config/sortOrder', $this->meta) + 1,
            ];

            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($pricePath, 0, -1) . '/base_price_button',
                $this->meta,
                $basePriceButton
            );
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function addBasePriceModal()
    {
        $this->meta['base-price']['arguments']['data']['config']['opened'] = true;
        $this->meta['base-price']['arguments']['data']['config']['collapsible'] = false;
        $this->meta['base-price']['arguments']['data']['config']['label'] = '';

        $this->meta['base_price_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Modal::NAME,
            'dataScope' => '',
            'provider' => 'product_form.product_form_data_source',
            'onCancel' => 'actionDone',
            'options' => [
                'title' => __('Base Price'),
                'buttons' => [
                    [
                        'text' => __('Done'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'actionDone'
                            ]
                        ]
                    ],
                ],
            ],
        ];

        $this->meta = $this->arrayManager->merge(
            $this->arrayManager->findPath(
                static::CONTAINER_PREFIX . ProductAttributeInterface::CODE_PRICE,
                $this->meta,
                null,
                'children'
            ),
            $this->meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'component' => 'Magento_Ui/js/form/components/group',
                        ],
                    ],
                ],
            ]
        );

        // replace select with switcher for element enable_base_price
        $this->meta = $this->arrayManager->merge(
            $this->arrayManager->findPath(
                static::CONTAINER_PREFIX . BasePriceProductAttributeInterface::CODE_ENABLE_BASE_PRICE,
                $this->meta,
                null,
                'children'
            ),
            $this->meta,
            [
                'children' => [
                    BasePriceProductAttributeInterface::CODE_ENABLE_BASE_PRICE => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataScope' => BasePriceProductAttributeInterface::CODE_ENABLE_BASE_PRICE,
                                    'additionalClasses' => 'admin__field-x-small',
                                    'formElement' => \Magento\Ui\Component\Form\Element\Checkbox::NAME,
                                    'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                                    'prefer' => 'toggle',
                                    'valueMap' => [
                                        'false' => '0',
                                        'true' => '1',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $this->meta['base_price_modal']['children']['base-price'] = $this->meta['base-price'];
        unset($this->meta['base-price']);
        return $this;
    }
}