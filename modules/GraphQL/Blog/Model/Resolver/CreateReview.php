<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Model\Resolver;

use GraphQL\Blog\Api\Data\CustomerReviewEntityInterface;
use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\Search\FilterGroupFactory;
use Magento\Framework\Api\SearchCriteriaInterfaceFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use GraphQL\Blog\Model\CustomerReviewRepository;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use GraphQL\Blog\Model\CustomerReviewFactory;

/**
 * Class CreateReview
 *
 * @package GraphQL\Blog\Model\Resolver
 */
class CreateReview implements ResolverInterface
{
    /**
     * @var CustomerReviewRepository
     */
    private $reviewRepository;

    /**
     * @var CustomerReviewFactory
     */
    private $reviewFactory;

    /**
     * @var FilterGroupFactory
     */
    private $filterGroupFactory;

    /**
     * @var SearchCriteriaInterfaceFactory
     */
    private $criteriaFactory;

    /**
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * CreateReview constructor.
     *
     * @param CustomerReviewRepository $reviewRepository
     * @param CustomerReviewFactory $reviewFactory
     * @param SearchCriteriaInterfaceFactory $criteriaFactory
     * @param FilterGroupFactory $filterGroupFactory
     * @param FilterFactory $filterFactory
     */
    public function __construct(
        CustomerReviewRepository $reviewRepository,
        CustomerReviewFactory $reviewFactory,
        SearchCriteriaInterfaceFactory $criteriaFactory,
        FilterGroupFactory $filterGroupFactory,
        FilterFactory   $filterFactory
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->reviewFactory = $reviewFactory;
        $this->filterGroupFactory   = $filterGroupFactory;
        $this->criteriaFactory = $criteriaFactory;
        $this->filterFactory = $filterFactory;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     * @throws \Exception
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
         *  - create
         *  mutation{
         *      createReview(
         *              customer_id: 7
         *              product_id: 100500
         *              customer_review: "Hi! I like coding!"
         *          ) {
         *              status
         *              message
         *              entity {
         *                  entity_id
         *                  product_id
         *                  customer_id
         *                  customer_review
         *          }
         *      }
         * }
         *
         * - update
         *  mutation{
         *      updateProductReviewByCustomerId(
         *          product_id: 3
         *          customer_id: 3
         *          customer_review: "New review"
         *      ) {
         *          status
         *          message,
         *          entity {
         *              entity_id
         *              product_id
         *              customer_id
         *              customer_review
         *          }
         *      }
         * }<
         */
        try {
            $this->validateFields($args);

            $reviewModel = $this->getReviewUsingRequestIds($args);
            if (!$reviewModel->getEntityId()) {
                $reviewModel->addData($args);
            } else {
                $reviewModel->setCustomerMessage($args['customer_review']);
            }

            $this->reviewRepository->save($reviewModel);
        } catch (GraphQlInputException $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
        return [
            'status'    => 200,
            'message'   => 'Awesome! It has been created successfully!',
            'entity'    => $reviewModel
        ];
    }

    /**
     * @param array $args
     * @return CustomerReviewEntityInterface
     */
    private function getReviewUsingRequestIds(array $args): CustomerReviewEntityInterface
    {
        $productId = $args['product_id'];
        $customerId = $args['customer_id'];

        $productFilter = $this->filterFactory->create()
            ->setField('product_id')
            ->setValue($productId)
            ->setConditionType('eq');

        $customerFilter = $this->filterFactory->create()
            ->setField('customer_id')
            ->setValue($customerId)
            ->setConditionType('eq');

        $productFilterGroup = $this->filterGroupFactory->create()
            ->setFilters([$productFilter]);
        $customerFilterGroup = $this->filterGroupFactory->create()
            ->setFilters([$customerFilter]);

        /** Trying to find review, which has already had customer_id and product_id.*/
        $searchCriteria = $this->criteriaFactory->create()
            ->setFilterGroups([$customerFilterGroup, $productFilterGroup]);

        $result = $this->reviewRepository->getList($searchCriteria);
        if ($result->getTotalCount() == 0) {
            return $this->reviewFactory->create();
        } else {
            $reviewModel = $result->getItems();
            return array_shift($reviewModel);
        }
    }

    /**
     * @param array $args
     * @throws GraphQlInputException
     */
    private function validateFields(array $args): void
    {
        if (!isset($args['customer_id'], $args['product_id'], $args['customer_review'])) {
            throw new GraphQlInputException(__('There isn\'t product_id and|or customer_id and|or customer_review'));
        }
    }
}
