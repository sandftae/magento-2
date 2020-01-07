<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use GraphQL\Blog\Model\CustomerReviewRepository;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;

/**
 * Class CustomerReviewGetById
 *
 * @package GraphQL\Blog\Model\Resolver
 */
class CustomerReviewGetById implements ResolverInterface
{
    /**
     * @var CustomerReviewRepository
     */
    private $reviewRepository;

    /**
     * @var ValueFactory
     */
    private $valueFactory;

    public function __construct(
        CustomerReviewRepository $reviewRepository,
        ValueFactory $valueFactory
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->valueFactory = $valueFactory;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value
     * @throws GraphQlInputException
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
         *  customerReviewGetById(id: 10, extract_field: "entity_id"){
         *     entity_id
         *     product_id
         *     customer_id
         *     customer_review
         *   }
         * }
         */

        if (!$args['id']) {
            throw new GraphQlInputException(__('There isn\'t id.'));
        }

        $entityId = (int)$args['id'];
        $extractingFields = isset($args['extract_field']) ? $args['extract_field'] : null;
        $result = function () use ($entityId, $extractingFields) {
            return $this->reviewRepository->getById($entityId, $extractingFields);
        };

        return $this->valueFactory->create($result);
    }
}
