<?php

namespace Sandftae\CustomShipping\Model\ResourceModel\Shipping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Sandftae\CustomShipping\Model\ResourceModel\Shipping
 */
class Collection extends AbstractCollection
{
    /**
     *  constructor Magento Way
     */
    protected function _construct()
    {
        $this->_init(
            'Sandftae\CustomShipping\Model\Shipping',
            'Sandftae\CustomShipping\Model\ResourceModel\Shipping'
        );
    }
}
