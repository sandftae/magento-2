<?php

namespace PleaseWork\setLabels\UI\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class LabelPosition
 * @package PleaseWork\setLabels\UI\Source
 */
class LabelPosition implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray():array
    {
        return [
            ['value' => 'not-selected', 'label' => __('not selected')],
            ['value' => 'top-left', 'label' => __('top-left')],
            ['value' => 'top-right', 'label' => __('top-right')],
            ['value' => 'bottom-left', 'label' => __('bottom-left')],
            ['value' => 'bottom-right', 'label' => __('bottom-right')]
        ];
    }
}
