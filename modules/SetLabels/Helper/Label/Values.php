<?php
/**
 * Magecom_SetLabels Constants::class
 *
 * @category Magecom
 * @package Magecom_Workshop
 * @author Magecom
 */
namespace PleaseWork\SetLabels\Helper\Label;

use PleaseWork\SetLabels\Helper\Label\Constants;
use PleaseWork\SetLabels\Block\LabelsSettings;

/**
 * Class Values
 * @package PleaseWork\SetLabels\Helper\Label
 */
class Values
{
    /**
     *  Block to total instance values to bundle products
     */
    public static $bundleSize;
    public static $bundleImage;
    public static $bundlePosition;
    public static $bundleOpacity;

    /**
     *  Block to total instance values to simple products
     */
    public static $simpleSize;
    public static $simpleImage;
    public static $simplePosition;
    public static $simpleOpacity;

    /**
     *  Block to total instance values to configurable products
     */
    public static $configurableSize;
    public static $configurableImage;
    public static $configurablePosition;
    public static $configurableOpacity;

    /**
     *  Block to total instance values to all products
     */
    public static $toAllSize;
    public static $toAllImage;
    public static $toAllPosition;
    public static $toAllOpacity;

    /**
     *  Block to total instance values to unique product
     */
    public static $uniqueSize;
    public static $uniqueImage;
    public static $uniquePosition;
    public static $uniqueOpacity;
    public static $uniqueSku;

    /**
     * @var $labelsSettings
     */
    public static $valuesConfig;

    /**
     * @param LabelsSettings $labelsSettings
     */
    public static function _construct(LabelsSettings $labelsSettings)
    {
        self::$valuesConfig = $labelsSettings;
    }

    /**
     * @return array
     */
    public static function getClassProperties():array
    {
        return get_class_vars(self::class);
    }

    /**
     * @return array
     */
    public static function getClassConstantsProperties():array
    {
        return get_class_vars(Constants::class);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function prepareNameToCompare(array $data):array
    {
        $arrVar = [];
        foreach ($data as $variable => $value) {
            if (stristr((string) $variable, '_')) {
                $data[$variable] = self::supportToCompare($variable);
            }
        }
        return $data;
    }

    /**
     * @param string $variable
     * @return string
     */
    public static function supportToCompare(string $variable):string
    {
        $data = null;
        $data = explode('_', $variable);
        return (string) strtolower($data[0]) . ucfirst($data[1]);
    }

    /**
     * @return void
     */
    public static function instance()
    {
        self::$bundleImage          = self::$valuesConfig->getScopeShow()->getValue(Constants::BUNDLE_IMAGE);
        self::$bundleOpacity        = self::$valuesConfig->getScopeShow()->getValue(Constants::BUNDLE_OPACITY);
        self::$bundlePosition       = self::$valuesConfig->getScopeShow()->getValue(Constants::BUNDLE_POSITION);
        self::$bundleSize           = self::$valuesConfig->getScopeShow()->getValue(Constants::BUNDLE_SIZE);

        self::$simpleImage          = self::$valuesConfig->getScopeShow()->getValue(Constants::SIMPLE_IMAGE);
        self::$simpleOpacity        = self::$valuesConfig->getScopeShow()->getValue(Constants::SIMPLE_OPACITY);
        self::$simplePosition       = self::$valuesConfig->getScopeShow()->getValue(Constants::SIMPLE_POSITION);
        self::$simpleSize           = self::$valuesConfig->getScopeShow()->getValue(Constants::SIMPLE_SIZE);

        self::$configurableSize     = self::$valuesConfig->getScopeShow()->getValue(Constants::CONFIGURABLE_SIZE);
        self::$configurableImage    = self::$valuesConfig->getScopeShow()->getValue(Constants::CONFIGURABLE_IMAGE);
        self::$configurablePosition = self::$valuesConfig->getScopeShow()->getValue(Constants::CONFIGURABLE_POSITION);
        self::$configurableOpacity  = self::$valuesConfig->getScopeShow()->getValue(Constants::CONFIGURABLE_OPACITY);

        self::$toAllSize            = self::$valuesConfig->getScopeShow()->getValue(Constants::ALL_SIZE);
        self::$toAllImage           = self::$valuesConfig->getScopeShow()->getValue(Constants::ALL_IMAGE);
        self::$toAllPosition        = self::$valuesConfig->getScopeShow()->getValue(Constants::ALL_POSITION);
        self::$toAllOpacity         = self::$valuesConfig->getScopeShow()->getValue(Constants::ALL_OPACITY);

        self::$uniqueImage          = self::$valuesConfig->getScopeShow()->getValue(Constants::UNIQUE_IMAGE);
        self::$uniqueOpacity        = self::$valuesConfig->getScopeShow()->getValue(Constants::UNIQUE_OPACITY);
        self::$uniquePosition       = self::$valuesConfig->getScopeShow()->getValue(Constants::UNIQUE_POSITION);
        self::$uniqueSize           = self::$valuesConfig->getScopeShow()->getValue(Constants::UNIQUE_SIZE);
        self::$uniqueSku            = self::$valuesConfig->getScopeShow()->getValue(Constants::UNIQUE_SKU);
    }
}
