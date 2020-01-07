<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Api\Data;

/**
 * Interface CustomerReviewEntityInterface
 *
 * @package GraphQL\Blog\Api\Data
 */
interface CustomerReviewEntityInterface
{
    /**#@+
     * Constants for keys of data array.
     */
    public const ENTITY_ID     = 'entity_id';
    public const PRODUCT_ID    = 'product_id';
    public const CUSTOMER_ID   = 'customer_id';
    public const CUSTOMER_REVIEW = 'customer_review';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId(): int;

    /**
     * @return int
     */
    public function getProductId(): int;

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void;

    /**
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId): void;

    /**
     * @return string
     */
    public function getCustomerMessage(): string;

    /**
     * @param string $msg
     */
    public function setCustomerMessage(string $msg): void;
}
