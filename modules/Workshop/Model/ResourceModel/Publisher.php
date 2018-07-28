<?php

namespace PleaseWork\Workshop\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Publisher
 * @package PleaseWork\Workshop\Model\ResourceModel
 */
class Publisher extends AbstractDb
{
    /**
     * Publisher constructor.
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(Context $context, $connectionName = null)
    {
        parent::__construct($context, $connectionName);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('pleasework_workshop_publisher', 'publisher_id');
    }
}
