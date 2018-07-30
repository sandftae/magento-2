<?php
/**
 * Magecom_SetLabels Image::class
 *
 * @category Magecom
 * @package Magecom_Workshop
 * @author Magecom
 */
namespace PleaseWork\SetLabels\Plugin\Model;

use PleaseWork\SetLabels\Block\LabelsSettings;
use Magento\Catalog\Helper\Image as MagentoImageHelper;
use \Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use \Magento\Framework\View\ConfigInterface;
use \Magento\Framework\View\Asset\Repository;
use \Magento\Catalog\Model\Product\ImageFactory;
use \Magento\Framework\App\Helper\Context;
use PleaseWork\SetLabels\Helper\Label\Values;

/**
 * Class Image
 * @package PleaseWork\SetLabels\Plugin\Model
 */
class Image extends MagentoImageHelper
{
    /**
     * @var LabelsSettings
     */
    protected $labelsSettings;

    /**
     * Image constructor.
     * @param Context $context
     * @param ImageFactory $productImageFactory
     * @param Repository $assetRepo
     * @param ConfigInterface $viewConfig
     * @param LabelsSettings $labelsSettings
     * @param Values $values
     * @param PlaceholderFactory|null $placeholderFactory
     * @throws \ReflectionException
     */
    public function __construct(
        Context $context,
        ImageFactory $productImageFactory,
        Repository $assetRepo,
        ConfigInterface $viewConfig,
        LabelsSettings $labelsSettings,
        Values $values,
        PlaceholderFactory $placeholderFactory = null
    ) {
        # pls, more construct methods in the same class
        $values::_construct($labelsSettings);

        parent::__construct(
            $context,
            $productImageFactory,
            $assetRepo,
            $viewConfig,
            $placeholderFactory
        );
        $this->labelsSettings = $labelsSettings;
    }

    /**
     * @param MagentoImageHelper $image
     * @param $imageObject
     * @return MagentoImageHelper
     */
    public function afterInit(MagentoImageHelper $image, $imageObject):MagentoImageHelper
    {
        Values::instance();
        $data = $imageObject->getProduct()->getData();
        $type = $data['type_id'];
        $sku = $data['sku'];

        # enabled/disabled logic

        if ($sku == Values::$uniqueSku) {
            $imageObject->watermark(
                Values::$uniqueImage,
                Values::$uniquePosition,
                Values::$uniqueSize,
                Values::$uniqueOpacity
            );
        } else {
            $this->instanceWatermark($type, $imageObject);
        }
        return $imageObject;
    }

    /**
     * @param string $type
     * @param MagentoImageHelper $imageObject
     * @return void
     */
    protected function instanceWatermark(string $type, MagentoImageHelper $imageObject)
    {
        switch ($type) {
            case 'simple':
                $imageObject->watermark(
                    Values::$simpleImage,
                    Values::$simplePosition,
                    Values::$simpleSize,
                    Values::$simpleOpacity
                );
                break;

            case 'bundle':
                $imageObject->watermark(
                    Values::$bundleImage,
                    Values::$bundlePosition,
                    Values::$bundleSize,
                    Values::$bundleOpacity
                );
                break;
            default:
                break;
        }
    }
}
