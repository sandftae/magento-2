<?php
/**
 * Magecom_SetLabels Dropdown::class
 *
 * @category Magecom
 * @package Magecom_Workshop
 * @author Magecom
 */
namespace PleaseWork\setLabels\UI\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Dropdown
 * @package PleaseWork\setLabels\UI\Source
 */
class Dropdown implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('not selected')],
            ['value' => 1, 'label' => __('New')],
            ['value' => 2, 'label' => __('Discount')],
            ['value' => 3, 'label' => __('Best seller')]
        ];
    }
}
