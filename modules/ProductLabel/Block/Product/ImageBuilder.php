<?php

namespace Magecom\ProductLabel\Block\Product;

use Magento\Catalog\Block\Product\ImageBuilder as ProductImageBuilder;
use Magento\Catalog\Helper\ImageFactory as HelperFactory;
use Magento\Catalog\Block\Product\ImageFactory;

/**
 * Class ImageBuilder
 */
class ImageBuilder extends ProductImageBuilder
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * ImageBuilder constructor.
     * @param HelperFactory $helperFactory
     * @param ImageFactory $imageFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        HelperFactory $helperFactory,
        ImageFactory $imageFactory,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->productResource = $productResource;
        $this->_storeManager = $storeManager;
        parent::__construct($helperFactory, $imageFactory);
    }

    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create()
    {
        /**
         * @var \Magento\Catalog\Helper\Image $helper
         */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);

        $template = $helper->getFrame()
            ? 'Magecom_ProductLabel::product/image.phtml'
            : 'Magecom_ProductLabel::product/image_with_borders.phtml';

        $imageSize = $helper->getResizedImageInfo();

        $labelSale = $this->getAttributeValue('label_sale');
        $labelNew = $this->getAttributeValue('label_new');
        $labelBestSeller = $this->getAttributeValue('label_bestsell');

        $data = [
            'data' => [
                'template' => $template,
                'image_url' => $helper->getUrl(),
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight(),
                'label' => $helper->getLabel(),
                'ratio' => $this->getRatio($helper),
                'custom_attributes' => $this->getCustomAttributes(),
                'resized_image_width' => !empty($imageSize[0]) ? $imageSize[0] : $helper->getWidth(),
                'resized_image_height' => !empty($imageSize[1]) ? $imageSize[1] : $helper->getHeight(),
                'label_new' => $labelNew,
                'label_sale' => $labelSale,
                'label_bestsell' => $labelBestSeller
            ],
        ];

        return $this->imageFactory->create($data);
    }

    /**
     * @param $name
     * @return array|bool|string
     */
    protected function getAttributeValue($name)
    {
        return $this->productResource->getAttributeRawValue(
            $this->product->getId(),
            $name,
            $this->_storeManager->getStore()->getId());
    }
}
