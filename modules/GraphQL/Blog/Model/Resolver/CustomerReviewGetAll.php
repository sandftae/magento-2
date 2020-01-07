<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use GraphQL\Blog\Model\CustomerReviewRepository;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;

/**
 * Class CustomerReviewGetAll
 *
 * @package GraphQL\Blog\Model\Resolver
 */
class CustomerReviewGetAll implements ResolverInterface
{
    /**
     * @var CustomerReviewRepository
     */
    private $reviewRepository;

    public function __construct(CustomerReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        /**
         * Example:
         *
         * query {
         *   customerReviewGetAll {
         *     items {
         *       entity_id
         *       product_id
         *       customer_id
         *       customer_review
         *    }
         *     total_count
         *   }
         * }
         */

        $reviewCollection = $this->reviewRepository->getList();
        return [
            'items' => $reviewCollection->getItems(),
            'total_count' => $reviewCollection->getTotalCount()
        ];
    }
}
