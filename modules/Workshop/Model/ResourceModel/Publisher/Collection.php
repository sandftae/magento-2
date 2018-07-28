<?php

namespace PleaseWork\Workshop\Model\ResourceModel\Publisher;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PleaseWork\Workshop\Model\ResourceModel\Publisher as PublisherResourece;
use PleaseWork\Workshop\Model\Publisher as PublisherModel;

/**
 * Class Collection
 * @package PleaseWork\Workshop\Model\ResourceModel\Publisher
 */
class Collection extends AbstractCollection
{
    # Reserves name a the variable
    protected $_idFieldName = 'publisher_id';

    # Reserves name a the variable
    protected $_eventPrefix = 'pleasework_workshop_publisher_collection';

    # Reserves name a the variable
    protected $_eventObject = 'publisher_collection';

    /**
     * Define collection
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->_init(PublisherModel::class, PublisherResourece::class);
    }
}