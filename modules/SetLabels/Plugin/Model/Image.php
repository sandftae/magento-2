<?php
/**
 * Magecom_SetLabels Image::class
 *
 * @category Magecom
 * @package Magecom_Workshop
 * @author Magecom
 */
namespace PleaseWork\SetLabels\Plugin\Model;

use Magento\Catalog\Model\Product\Image as MagentoProductImage;
use PleaseWork\SetLabels\Block\LabelsSettings;
use PleaseWork\SetLabels\Helper\Label\Constants;
use PleaseWork\SetLabels\Helper\Label\ParseValues;

/**
 * Class Image
 * @package PleaseWork\SetLabels\Plugin\Model
 */
class Image
{
    /**
     * @var LabelsSettings
     */
    protected $labelsSettings;

    /**
     * Image constructor.
     * @param LabelsSettings $labelsSettings
     * @param ParseValues $helper
     */
    public function __construct(LabelsSettings $labelsSettings, ParseValues $helper)
    {
        $this->labelsSettings = $labelsSettings;
    }

    /**
     * @param MagentoProductImage $image
     * @param $position
     * @return array
     */
    public function beforeSetWatermarkPosition(MagentoProductImage $image, $position):array
    {
        return [$this->labelsSettings->getScopeShow()->getValue(Constants::ALL_POSITION)];
    }

    /**
     * @param MagentoProductImage $image
     * @param $file
     * @return array
     */
    public function beforeSetWatermarkFile(MagentoProductImage $image, $file):array
    {
        return [$this->labelsSettings->getScopeShow()->getValue(Constants::ALL_IMAGE)];
    }

    /**
     * @param MagentoProductImage $image
     * @param $opacity
     * @return array
     */
    public function beforeSetWatermarkImageOpacity(MagentoProductImage $image, $opacity):array
    {
        return [$this->labelsSettings->getScopeShow()->getValue(Constants::ALL_OPACITY)];
    }

    /**
     * @param MagentoProductImage $image
     * @param $size
     * @return array
     */
    public function beforeSetWatermarkSize(MagentoProductImage $image, $size):array
    {
        $size = $this->labelsSettings->getScopeShow()->getValue(Constants::ALL_SIZE);
        return [ParseValues::parseSize($size)];
    }
}
