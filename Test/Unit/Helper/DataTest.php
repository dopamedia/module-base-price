<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.01.17
 */

namespace Dopamedia\BasePrice\Test\Unit\Helper;

use Dopamedia\BasePrice\Helper\Data;
use Dopamedia\Measure\Model\BuilderInterface;
use Dopamedia\Measure\Model\UnitConverterInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    /**
     * @var MockObject|\Magento\Framework\App\Helper\Context
     */
    protected $contextMock;

    /**
     * @var MockObject|UnitConverterInterface
     */
    protected $unitConverterMock;

    /**
     * @var MockObject|BuilderInterface
     */
    protected $measureBuilderMock;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var MockObject|\Magento\Framework\Pricing\SaleableInterface
     */
    protected $salableItemMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->contextMock = $this->getMock('\Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->unitConverterMock = $this->getMock('\Dopamedia\Measure\Model\UnitConverterInterface');
        $this->measureBuilderMock = $this->getMock('\Dopamedia\Measure\Model\BuilderInterface');
        $this->helper = new Data($this->contextMock, $this->unitConverterMock, $this->measureBuilderMock);
        $this->salableItemMock = $this->getMockBuilder('Magento\Framework\Pricing\SaleableInterface')
            ->setMethods([
                'getPriceInfo',
                'getTypeId',
                'getId',
                'getQty',
                'getData',
                'getFinalPrice'
            ])
            ->getMock();
    }

    /**
     * @covers Data::calculateBasePrice()
     */
    public function testCalculateBasePrice()
    {
        $this->unitConverterMock->expects($this->once())
            ->method('convert')
            ->willReturn(100);

        $this->salableItemMock->expects($this->at(0))
            ->method('getData')
            ->with('base_price_reference_amount')
            ->willReturn(500);

        $this->salableItemMock->expects($this->once())
            ->method('getFinalPrice')
            ->willReturn(10);

        $this->assertEquals(50, $this->helper->calculateBasePrice($this->salableItemMock));
    }

    /**
     * @covers Data::getReferenceUnit()
     */
    public function testGetReferenceUnit()
    {
        $this->measureBuilderMock->expects($this->once())
            ->method('getUnit')
            ->willReturn(
                $this->getMock('\Dopamedia\Measure\Api\Data\UnitInterface')
            );

        $this->assertInstanceOf(
            '\Dopamedia\Measure\Api\Data\UnitInterface',
            $this->helper->getReferenceUnit($this->salableItemMock)
        );
    }
}
