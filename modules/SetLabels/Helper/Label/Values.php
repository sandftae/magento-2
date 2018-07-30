<?php
/**
 * Magecom_SetLabels Constants::class
 *
 * @category Magecom
 * @package Magecom_Workshop
 * @author Magecom
 */
namespace PleaseWork\SetLabels\Helper\Label;

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
    private static $valuesConfig;

    /**
     * @var $reflection
     */
    private static $reflectionConstants;

    /**
     * @param LabelsSettings $labelsSettings
     * @throws \ReflectionException
     */
    public static function _construct(
        LabelsSettings $labelsSettings
    ) {
        self::$valuesConfig = $labelsSettings;
        self::$reflectionConstants =  new \ReflectionClass(Constants::class);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function prepareNameToCompare(array $data):array
    {
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
        $data = explode('_', $variable);
        return (string) strtolower($data[0]) .  ucfirst(strtolower($data[1]));
    }

    /**
     * @return void
     */
    public static function instance()
    {
        $constants = self::prepareNameToCompare(self::$reflectionConstants->getConstants());

        foreach ($constants as $key => $constant) {
            if (property_exists(self::class, $constant)) {
                $constKey = self::$reflectionConstants->getConstant($key);
                static::$$constant = self::$valuesConfig->getScopeShow()->getValue($constKey);
            }
        }
    }
}
