<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Model\ResourceModel\CustomerReview;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use GraphQL\Blog\Model\ResourceModel\CustomerReview as CustomerReviewResource;
use GraphQL\Blog\Model\CustomerReview as CustomerReviewModel;

/**
 * Class Collection
 *
 * @package GraphQL\Blog\Model\ResourceModel\CustomerReview
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(CustomerReviewModel::class, CustomerReviewResource::class);
    }
}
