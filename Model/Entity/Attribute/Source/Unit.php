<?php
/**
 * User: Andreas Penz <office@dopa.media>
 * Date: 06.01.17
 */

namespace Dopamedia\BasePrice\Model\Entity\Attribute\Source;

class Unit extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Dopamedia\Measure\Api\UnitListInterface
     */
    private $unitList;

    /**
     * @inheritDoc
     */
    public function __construct(\Dopamedia\Measure\Api\UnitListInterface $unitList)
    {
        $this->unitList = $unitList;
    }

    /**
     * @param bool $withEmpty
     * @return array|null
     */
    public function getAllOptions($withEmpty = true)
    {
        if ($this->_options === null) {
            foreach ($this->unitList->getUnits() as $unit) {
                $this->_options[] = [
                    'label' => __($unit->getName()),
                    'value' => $unit->getCode()
                ];
            }

            if ($withEmpty) {
                array_unshift($this->_options, [
                    'label' => __('-- Please Select --'),
                    'value' => ''
                ]);
            }
        }
        return $this->_options;
    }
}