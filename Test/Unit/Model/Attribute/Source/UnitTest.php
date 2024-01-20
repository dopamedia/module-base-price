<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 19.01.17
 */

namespace Dopamedia\BasePrice\Test\Unit\Model\Attribute\Source;

use Dopamedia\BasePrice\Model\Entity\Attribute\Source\Unit;
use Magento\Framework\DataObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
{

    /**
     * @var MockObject|\Dopamedia\Measure\Api\UnitListInterface
     */
    protected $unitListMock;

    /**
     * @var Unit
     */
    protected $unit;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->unitListMock = $this->getMock('\Dopamedia\Measure\Api\UnitListInterface');
        $this->unit = new Unit($this->unitListMock);
    }

    /**
     * @covers Unit::getAllOptions()
     */
    public function testGetAllOptionsReturnsNull()
    {
        $this->unitListMock->expects($this->once())
            ->method('getUnits')
            ->willReturn([]);
        $this->assertNull($this->unit->getAllOptions(false));
    }

    /**
     * @covers Unit::getAllOptions()
     */
    public function testGetAllOptionsWithEmpty()
    {
        $units = [
            new DataObject(['name' => 'First', 'code' => 'first']),
            new DataObject(['name' => 'Second', 'code' => 'second'])
        ];

        $this->unitListMock->expects($this->once())
            ->method('getUnits')
            ->willReturn($units);

        $expected = [
            [
                'label' => '-- Please Select --',
                'value' => ''
            ],
            [
                'label' => 'First',
                'value' => 'first'
            ],
            [
                'label' => 'Second',
                'value' => 'second'
            ]
        ];

        $this->assertEquals($expected, $this->unit->getAllOptions());
    }

    /**
     * @covers Unit::getAllOptions()
     */
    public function testGetAllOptionsWithoutEmpty()
    {
        $units = [
            new DataObject(['name' => 'First', 'code' => 'first']),
            new DataObject(['name' => 'Second', 'code' => 'second'])
        ];

        $this->unitListMock->expects($this->once())
            ->method('getUnits')
            ->willReturn($units);

        $expected = [
            [
                'label' => 'First',
                'value' => 'first'
            ],
            [
                'label' => 'Second',
                'value' => 'second'
            ]
        ];
        $this->assertEquals($expected, $this->unit->getAllOptions(false));
    }
}
