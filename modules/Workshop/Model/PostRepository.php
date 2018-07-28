<?php

namespace PleaseWork\Workshop\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use PleaseWork\Workshop\Api\Data\PostInterface;
use PleaseWork\Workshop\Model\ResourceModel\Post\Collection;
use PleaseWork\Workshop\Model\ResourceModel\Post as PostResource;
use PleaseWork\Workshop\Model\Post as PostModel;
use PleaseWork\Workshop\Model\PostFactory;
use PleaseWork\Workshop\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class PostRepository
 * @package PleaseWork\Workshop\Model
 */
class PostRepository implements PostInterface
{
    /**
     * @var PostResource
     */
    protected $resourseModel;

    /**
     * @var Post
     */
    protected $model;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var \PleaseWork\Workshop\Model\PostFactory
     */
    protected $postFactory;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchResultsFactory;

    /**
     * PostRepository constructor.
     * @param Post $postModel
     * @param PostResource $postResource
     * @param CollectionFactory $collection
     * @param \PleaseWork\Workshop\Model\PostFactory $postFactory
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultFactory $searchResultsFactory
     */
    public function __construct(
        PostModel $postModel,
        PostResource $postResource,
        CollectionFactory $collection,
        PostFactory $postFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultFactory $searchResultsFactory
    ) {
        $this->resourseModel            = $postResource;
        $this->model                    = $postModel;
        $this->postFactory              = $postFactory;
        $this->collection               = $collection;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->filterBuilder            = $filterBuilder;
        $this->collectionProcessor      = $collectionProcessor;
        $this->searchResultsFactory     = $searchCriteriaBuilder;
    }

    /**
     * @param $objectModel
     * @return mixed|void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save($objectModel)
    {
        $this->resourseModel->save($objectModel);
    }

    /**
     * @param $model
     * @param $id
     * @return mixed|void
     * @throws \Exception
     */
    public function delete($model, $id)
    {
        $model = $this->postFactory->create();
        $this->resourseModel->load($model, $id);
        $this->resourseModel->delete($model);
    }

    /**
     * @return mixed|Collection
     */
    public function getList()
    {
        return $this->collection->create();
    }

    /**
     * @param $id
     * @return mixed|PostResource
     */
    public function getById($id)
    {
        $post = $this->postFactory->create();
        $this->resourseModel->load($post, $id);
        return  $post;
    }

    /**
     * @return Post
     */
    public function createObject()
    {
        $emptyModel = $this->postFactory->create();
        return $emptyModel;
    }

    /**
     * @return mixed
     */
    public function getFilterList()
    {
        $filter = $this->filterBuilder
                         ->setField('status')
                         ->setValue(0)
                         ->setConditionType('eq')
                         ->create();

        $search = $this->searchCriteriaBuilder
                        ->addFilter($filter)
                        ->create();

        # return $search;

        # echo $this->collection->__toString();exit();
        return $this->collection->create()
                                ->addFieldToFilter('status', 'eq', 0)
                                ->load();

        # return $this->collection->create()
        #                         ->addFieldToFilter('status', 'eq', 0)
        #                         ->load();
    }

    /**
     * @param SearchCriteriaInterface|null $criteria
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    public function getListTwo(SearchCriteriaInterface $criteria = null)
    {
        $collection = $this->collection->create();

        $this->collectionProcessor->process($criteria, $collection);


        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
