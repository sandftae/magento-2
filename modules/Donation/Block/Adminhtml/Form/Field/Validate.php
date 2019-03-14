<?php

namespace Sandftae\Donation\Block\Adminhtml\Form\Field;

use Magento\Config\Model\Config\Backend\Serialized;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Validate
 * @package Sandftae\Donation\Block\Adminhtml\Form\Field
 */
class Validate extends Serialized
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * Validate constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null

    ) {
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data,
            $serializer
        );
    }

    /**
     * @param array $values
     */
    private function convertRatePriceToFloat(array &$values)
    {
        foreach ($values as $key => $value) {
            if (is_array($value) && array_key_exists('rate_price', $value)) {
                $ratePrice = preg_replace('/\s+/', '', $value['rate_price']);
                $values[$key]['rate_price'] = (float)$ratePrice;
            }
        }
    }

    /**
     * @return Serialized|void
     */
    public function beforeSave()
    {
        if (!empty($this->getValue()) && is_array($this->getValue())) {
            $values = $this->getValue();
            $this->convertRatePriceToFloat($values);
            if (array_key_exists('__empty', $values)) {
                unset($values['__empty']);
            }
            $ser = $this->serializer->serialize($values);
            $this->setValue($ser);
        }
    }
}
