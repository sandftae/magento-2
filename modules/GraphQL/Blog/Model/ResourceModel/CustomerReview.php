<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use GraphQL\Blog\Api\Data\CustomerReviewEntityInterface;

/**
 * Class CustomerReview
 *
 * @package GraphQL\Blog\Model\ResourceModel
 */
class CustomerReview extends AbstractDb
{
    /**
     * Name of main table
     */
    public const TABLE_NAME = 'customer_review';

    /**
     * @inheritDoc
     */
    public function _construct(): void
    {
        $this->_init(static::TABLE_NAME, 'entity_id');
    }

    /**
     * Get main table name
     *
     * @return string
     */
    public function getMainTable(): string
    {
        return $this->getTable(self::TABLE_NAME);
    }
}
