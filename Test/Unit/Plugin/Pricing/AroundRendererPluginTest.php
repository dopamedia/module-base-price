<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.01.17
 */

namespace Dopamedia\BasePrice\Test\Unit\Plugin\Pricing;

use Dopamedia\BasePrice\Plugin\Pricing\AroundRendererPlugin;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AroundRendererPluginTest extends TestCase
{
    /**
     * @var MockObject|\Magento\Framework\Pricing\Render
     */
    protected $subjectMock;

    /**
     * @var MockObject|\Magento\Framework\Pricing\SaleableInterface
     */
    protected $saleableItemMock;

    /**
     * @var AroundRendererPlugin
     */
    protected $aroundRendererPlugin;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->subjectMock = $this->getMock('\Magento\Framework\Pricing\Render', [], [], '', false);

        $this->saleableItemMock = $this->getMockBuilder('Magento\Framework\Pricing\SaleableInterface')
            ->setMethods([
                'getPriceInfo',
                'getTypeId',
                'getId',
                'getQty',
                'getData',
                'getFinalPrice'
            ])
            ->getMock();

        $this->aroundRendererPlugin = new AroundRendererPlugin();
    }

    /**
     * @covers AroundRendererPlugin::aroundRender()
     */
    public function testAroundRenderReturnsEmptyString()
    {
        $closureMock = function () {
            return '';
        };

        $this->assertEquals(
            '',
            $this->aroundRendererPlugin->aroundRender(
                $this->subjectMock,
                $closureMock,
                '',
                $this->saleableItemMock
            )
        );
    }

    /**
     * @covers AroundRendererPlugin::aroundRender()
     */
    public function testAroundRenderAttachesBasePriceBlock()
    {
        $closureMock = function () {
            return 'proceedResultHtml - ';
        };

        $blockMock = $this->getMockBuilder('Magento\Framework\View\Element\BlockInterface')
            ->setMethods(['setSaleableItem', 'toHtml'])
            ->getMock();

        $blockMock->expects($this->once())
            ->method('setSaleableItem')
            ->willReturnSelf();

        $blockMock->expects($this->once())
            ->method('toHtml')
            ->willReturn('additionalResultHtml');

        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface');

        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('baseprice.product.price.base_price')
            ->willReturn($blockMock);


        $this->subjectMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($layoutMock);

        $this->assertEquals(
            'proceedResultHtml - additionalResultHtml',
            $this->aroundRendererPlugin->aroundRender(
                $this->subjectMock,
                $closureMock,
                '',
                $this->saleableItemMock
            )
        );
    }
}