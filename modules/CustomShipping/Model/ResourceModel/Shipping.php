<?php

namespace Sandftae\CustomShipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Shipping
 * @package Sandftae\CustomShipping\Model\ResourceModel
 */
class Shipping extends AbstractDb
{
    /**
     *  constructor Magento Way
     */
    protected function _construct()
    {
        $this->_init(
            'sandftae_shipping_countries',
            'id'
        );
    }
}
