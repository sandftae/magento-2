<?php

namespace Sandftae\CustomShipping\Model;

use Magento\Framework\Model\AbstractModel;
use Sandftae\CustomShipping\Model\ResourceModel\Shipping as ShippingResource;

/**
 * Class Shipping
 *
 * @package Sandftae\CustomShipping\Model
 */
class Shipping extends AbstractModel
{
    /**
     *  constructor Magento Way
     */
    protected function _construct()
    {
        $this->_init(ShippingResource::class);
    }
}
