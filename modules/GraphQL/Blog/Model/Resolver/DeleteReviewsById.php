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
use GraphQL\Blog\Model\CustomerReviewFactory;
use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\SearchCriteriaInterfaceFactory;
use Magento\Framework\Api\Search\FilterGroupFactory;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class DeleteReviewsById
 *
 * @package GraphQL\Blog\Model\Resolver
 */
class DeleteReviewsById implements ResolverInterface
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
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * @var FilterFactory
     */
    private $filterFactory;

    /**
     * @var SearchCriteriaInterfaceFactory
     */
    private $criteriaFactory;

    /**
     * @var FilterGroupFactory
     */
    private $filterGroupFactory;

    /**
     * DeleteReviewsById constructor.
     *
     * @param SearchCriteriaInterfaceFactory $criteriaFactory
     * @param CustomerReviewRepository $reviewRepository
     * @param CustomerReviewFactory $reviewFactory
     * @param FilterGroupFactory $filterGroupFactory
     * @param ValueFactory $valueFactory
     * @param FilterFactory $filterFactory
     */
    public function __construct(
        SearchCriteriaInterfaceFactory $criteriaFactory,
        CustomerReviewRepository $reviewRepository,
        CustomerReviewFactory $reviewFactory,
        FilterGroupFactory $filterGroupFactory,
        ValueFactory    $valueFactory,
        FilterFactory   $filterFactory
    ) {
        $this->reviewRepository     = $reviewRepository;
        $this->filterGroupFactory   = $filterGroupFactory;
        $this->criteriaFactory = $criteriaFactory;
        $this->reviewFactory = $reviewFactory;
        $this->valueFactory  = $valueFactory;
        $this->filterFactory = $filterFactory;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            /**
             * mutation{
             *          deleteReviewById(
             *          id: 100500
             *          extract_field: "product_id"
             *      ) {
             *          status
             *          message
             *      }
             * }
             */
            if (!isset($args['id'])) {
                throw new GraphQlInputException(__('There isn\'t  id'));
            }

            $field = $args['extract_field'];
            $fieldValue = $args['id'];
            $filterId = $this->filterFactory->create()
                                          ->setField($field)
                                          ->setValue($fieldValue)
                                          ->setConditionType('eq');

            $filterGroup = $this->filterGroupFactory->create()
                                                    ->setFilters([$filterId]);

            $searchCriteria = $this->criteriaFactory->create()
                                                    ->setFilterGroups([$filterGroup]);

            $result = $this->reviewRepository->getList($searchCriteria);

            if ($result->getTotalCount() == 0) {
                throw new GraphQlInputException(__(
                    sprintf('There isn\'t id like %s or field like %s', $fieldValue, $field)
                ));
            }

            $this->delete($result);
            return [
                'status'    => 200,
                'message'   => 'All reviews has been deleted!',
                'entity'    => null
            ];
        } catch (CouldNotSaveException $e) {
            return [
                'status'    => 500,
                'message'   => $e->getMessage(),
                'entity'    => null
            ];
        }
    }

    /**
     * @param $reviewsModel
     * @throws CouldNotSaveException
     */
    private function delete($reviewsModel): void
    {
        $reviewsModel = $reviewsModel->getItems();
        foreach ($reviewsModel as $model) {
            $this->reviewRepository->delete($model);
        }
    }
}
