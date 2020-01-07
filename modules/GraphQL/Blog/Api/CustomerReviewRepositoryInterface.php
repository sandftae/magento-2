<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Api;

use GraphQL\Blog\Api\Data\CustomerReviewEntityInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CustomerReviewRepositoryInterface
 *
 * @package GraphQL\Blog\Api
 */
interface CustomerReviewRepositoryInterface
{
    /**
     * @param CustomerReviewEntityInterface $reviewEntity
     */
    public function save(CustomerReviewEntityInterface $reviewEntity): void;

    /**
     * @param int $entityId
     * @param string|null $field
     * @return CustomerReviewEntityInterface
     */
    public function getById(int $entityId, string $field = null): CustomerReviewEntityInterface;

    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): SearchResultsInterface;
}
