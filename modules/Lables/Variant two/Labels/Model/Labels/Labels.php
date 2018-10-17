<?php

namespace PleaseWork\Labels\Model\Labels;

use Magento\Catalog\Model\ProductRepository;
use PleaseWork\Labels\Model\Labels\Settings\LabelsSettings;
use PleaseWork\Labels\Helper\Config\InstanceValues;

/**
 * Class Labels
 * @package PleaseWork\Labels\Model\Labels
 */
class Labels
{
    /**
     * @var ProductRepository
     */
    public $productRepository;

    /**
     * @var LabelsSettings
     */
    public $labelsSettings;

    /**
     * @var InstanceValues
     */
    public $instanceValues;

    /**
     * @var $image
     */
    public $image;

    /**
     * @var $position
     */
    public $position;

    /**
     * @var $size
     */
    public $size;

    /**
     * @var $opacity
     */
    public $opacity;

    /**
     * Labels constructor.
     * @param ProductRepository $productRepository
     * @param LabelsSettings $labelsSettings
     * @param InstanceValues $instanceValues
     */
    public function __construct(
        ProductRepository $productRepository,
        LabelsSettings $labelsSettings,
        InstanceValues $instanceValues
    ) {
        $this->productRepository = $productRepository;
        $this->labelsSettings    = $labelsSettings;
        $this->instanceValues    = $instanceValues;
        $instanceValues->instance();
    }

    /**
     * Get the type of label from the product
     *
     * @param int $id
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getTypeLabels(int $id):string
    {
        return $this->productRepository->getById($id)->getAttributeText('custom_labels_images');
    }

    /**
     * Dynamically set the values ​​of variables
     *
     * @param int $id
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function defineValues(int $id)
    {
        $typeLabel = $this->getTypeLabels($id);

        switch ($typeLabel) {
            case 'new':
                $this->supportDefineValues($typeLabel);
                break;
            case 'discount':
                $this->supportDefineValues($typeLabel);
                break;
            case 'bestseller':
                $this->supportDefineValues($typeLabel);
                break;
        }
    }

    /**
     * Dynamically set the values ​​of variables
     *
     * @param string $type
     * @return void
     */
    protected function supportDefineValues(string $type)
    {
        $this->size = $this->instanceValues->{$type . 'Size'};
        $this->image = $this->instanceValues->{$type . 'Label'};
        $this->opacity = $this->instanceValues->{$type . 'Opacity'};
        $this->position = $this->instanceValues->{$type . 'Position'};
    }
}
