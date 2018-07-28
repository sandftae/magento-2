<?php

namespace PleaseWork\Workshop\Model\ResourceModel\Post;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use PleaseWork\Workshop\Model\ResourceModel\Post as PostResource;
use PleaseWork\Workshop\Model\Post  as PostModel;

class Collection extends AbstractCollection
{
    # Reserves name a the variable
    protected $_idFieldName = 'post_id';

    # Reserves name a the variable
    protected $_eventPrefix = 'pleasework_workshop_post_collection';

    # Reserves name a the variable
    protected $_eventObject = 'post_collection';

    /**
     * Define collection
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $this->_init(PostModel::class, PostResource::class);
    }
}
