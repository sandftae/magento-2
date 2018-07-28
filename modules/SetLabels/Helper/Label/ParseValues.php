<?php
/**
 * Magecom_SetLabels ParseValues::class
 *
 * @category Magecom
 * @package Magecom_Workshop
 * @author Magecom
 */
namespace PleaseWork\SetLabels\Helper\Label;

/**
 * Class ParseValues
 * @package PleaseWork\SetLabels\Helper\Label
 */
class ParseValues
{
    /**
     * @param $string
     * @return array|bool
     */
    public static function parseSize($string)
    {
        $size = explode('x', strtolower($string));
        if (sizeof($size) == 2) {
            return ['width' => $size[0] > 0 ? $size[0] : null, 'height' => $size[1] > 0 ? $size[1] : null];
        }
        return false;
    }
}
