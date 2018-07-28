<?php

namespace PleaseWork\Workshop\Model\ResourceModel\FeedBack;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PleaseWork\Workshop\Model\ResourceModel\FeedBack as FeedBackResource;
use PleaseWork\Workshop\Model\FeedBack  as FeedBackModel;

class Collection extends AbstractCollection
{
    # Reserves name a the variable
    protected $_idFieldName = 'message_id';

    # Reserves name a the variable
    protected $_eventPrefix = 'pleasework_workshop_feedback_collection';

    # Reserves name a the variable
    protected $_eventObject = 'feedback_collection';

    /**
     * Define collection
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->_init(FeedBackModel::class, FeedBackResource::class);
    }
}
