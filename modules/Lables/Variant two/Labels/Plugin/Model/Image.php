<?php

namespace PleaseWork\Labels\Plugin\Model;

use Magento\Catalog\Helper\Image as MagentoImageHelper;
use \Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use \Magento\Framework\View\ConfigInterface;
use \Magento\Framework\View\Asset\Repository;
use \Magento\Catalog\Model\Product\ImageFactory;
use \Magento\Framework\App\Helper\Context;
use PleaseWork\Labels\Helper\Config\InstanceValues;

/**
 * Class Image
 * @package PleaseWork\SetLabels\Plugin\Model
 */
class Image extends MagentoImageHelper
{
    /**
     * @var $labelsSettings
     */
    protected $labelsSettings;

    /**
     * @var InstanceValues
     */
    protected $instance;

    /**
     * @var \PleaseWork\Labels\Model\Labels\Labels
     */
    public $labels;

    /**
     * Image constructor.
     * @param Context $context
     * @param ImageFactory $productImageFactory
     * @param Repository $assetRepo
     * @param ConfigInterface $viewConfig
     * @param \PleaseWork\Labels\Model\Labels\Labels $labels
     * @param PlaceholderFactory|null $placeholderFactory
     */
    public function __construct(
        Context $context,
        ImageFactory $productImageFactory,
        Repository $assetRepo,
        ConfigInterface $viewConfig,
        \PleaseWork\Labels\Model\Labels\Labels $labels,
        PlaceholderFactory $placeholderFactory = null
    ) {
        parent::__construct(
            $context,
            $productImageFactory,
            $assetRepo,
            $viewConfig,
            $placeholderFactory
        );
        $this->labels = $labels;
    }

    /**
     * @param MagentoImageHelper $image
     * @param $imageObject
     * @return MagentoImageHelper
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterInit(MagentoImageHelper $image, $imageObject):MagentoImageHelper
    {
        # enabled/disabled logic

        $data = $imageObject->getProduct()->getData();
        $id = $data['entity_id'];
        $this->labels->defineValues($id);

        $imageObject->watermark(
            $this->labels->image,
            $this->labels->position,
            $this->labels->size,
            $this->labels->opacity
        );
        return $imageObject;
    }
}
