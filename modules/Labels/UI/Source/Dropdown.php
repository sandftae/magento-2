<?php

namespace Sandftae\setLabels\UI\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Dropdown
 * @package Sandftae\setLabels\UI\Source
 */
class Dropdown implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray():array
    {
        return [
            ['value' => 0, 'label' => __('not selected')],
            ['value' => 1, 'label' => __('New')],
            ['value' => 2, 'label' => __('Discount')],
            ['value' => 3, 'label' => __('Best seller')]
        ];
    }
}
