<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use GraphQL\Blog\Model\ResourceModel\CustomerReview as CustomerReviewResource;
use GraphQL\Blog\Api\Data\CustomerReviewEntityInterface;

/**
 * Class CustomerReview
 *
 * @package GraphQL\Blog\Model
 */
class CustomerReview extends AbstractModel implements CustomerReviewEntityInterface
{
    /**
     * Init is here
     */
    protected function _construct()
    {
        $this->_init(CustomerReviewResource::class);
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return (int) $this->getData(static::CUSTOMER_ID);
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return (int) $this->getData(static::PRODUCT_ID);
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void
    {
        $this->setData(static::PRODUCT_ID, $productId);
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return (int) $this->getData(static::CUSTOMER_ID);
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId): void
    {
        $this->setData(static::CUSTOMER_ID, $customerId);
    }

    /**
     * @return string
     */
    public function getCustomerMessage(): string
    {
        return (string) $this->getData(static::CUSTOMER_REVIEW);
    }

    /**
     * @param string $msg
     */
    public function setCustomerMessage(string $msg): void
    {
        $this->setData(static::CUSTOMER_REVIEW, $msg);
    }
}
