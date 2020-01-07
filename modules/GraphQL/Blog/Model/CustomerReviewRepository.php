<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Model;

use GraphQL\Blog\Api\Data\CustomerReviewEntityInterface;
use GraphQL\Blog\Model\ResourceModel\CustomerReview\CollectionFactory as CustomerReviewCollectionFactory;
use GraphQL\Blog\Model\ResourceModel\CustomerReview\Collection as ReviewCollection;
use GraphQL\Blog\Model\ResourceModel\CustomerReview as CustomerReviewResource;
use GraphQL\Blog\Api\Data\CustomerReviewEntityInterfaceFactory;
use GraphQL\Blog\Api\CustomerReviewRepositoryInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * Class CustomerReviewRepository
 *
 * @package GraphQL\Blog\Model
 */
class CustomerReviewRepository implements CustomerReviewRepositoryInterface
{
    /**
     * @var CustomerReviewCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CustomerReviewResource
     */
    private $reviewResource;

    /**
     * @var CustomerReviewEntityInterfaceFactory
     */
    private $reviewFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * CustomerReviewRepository constructor.
     *
     * @param CustomerReviewEntityInterfaceFactory $reviewFactory
     * @param CustomerReviewCollectionFactory $collectionFactory
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerReviewResource $resource
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomerReviewEntityInterfaceFactory $reviewFactory,
        CustomerReviewCollectionFactory      $collectionFactory,
        SearchResultsInterfaceFactory        $searchResultsFactory,
        CollectionProcessorInterface         $collectionProcessor,
        SearchCriteriaBuilder       $searchCriteriaBuilder,
        CustomerReviewResource      $resource,
        LoggerInterface             $logger
    ) {
        $this->collectionProcessor   = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultsFactory  = $searchResultsFactory;
        $this->reviewResource        = $resource;
        $this->collectionFactory     = $collectionFactory;
        $this->reviewFactory         = $reviewFactory;
        $this->logger                = $logger;
    }

    /**
     * @param CustomerReviewEntityInterface $reviewEntity
     * @throws \Exception
     */
    public function save(CustomerReviewEntityInterface $reviewEntity): void
    {
        try {
            $this->reviewResource->save($reviewEntity);
        } catch (AlreadyExistsException $e) {
            $this->logger->critical('Something has gone wrong. ' . $e->getMessage());
        }
    }

    /**
     * @param CustomerReviewEntityInterface $reviewEntity
     * @throws CouldNotSaveException
     */
    public function delete(CustomerReviewEntityInterface $reviewEntity): void
    {
        try {
            $this->reviewResource->delete($reviewEntity);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
    }

    /**
     * @param int $entityId
     * @param string|null $field
     * @return CustomerReviewEntityInterface
     */
    public function getById(int $entityId, string $field = null): CustomerReviewEntityInterface
    {
        $reviewModel = $this->reviewFactory->create();
        $this->reviewResource->load($reviewModel, $entityId, $field);
        return $reviewModel;
    }

    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null): SearchResultsInterface
    {
        /** @var ReviewCollection $reviewCollection */
        $reviewCollection = $this->collectionFactory->create();
        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $reviewCollection);
        }
        /** @var SearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setItems($reviewCollection->getItems());
        $searchResult->setTotalCount($reviewCollection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}
