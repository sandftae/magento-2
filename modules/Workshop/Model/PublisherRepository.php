<?php

namespace PleaseWork\Workshop\Model;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use PleaseWork\Workshop\Api\Data\PublisherInterface;
use PleaseWork\Workshop\Model\ResourceModel\Publisher\Collection;
use PleaseWork\Workshop\Model\ResourceModel\Publisher as PublisherResource;
use PleaseWork\Workshop\Model\Publisher as PublisherModel;
use PleaseWork\Workshop\Model\PublisherFactory;
use PleaseWork\Workshop\Model\ResourceModel\Publisher\CollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\Search\SearchResultFactory;

/**
 * Class PublisherRepository
 * @package PleaseWork\Workshop\Model
 */
class PublisherRepository
{
    /**
     * @var PublisherResource
     */
    protected $resourceModel;

    /**
     * @var Publisher
     */
    protected $model;

    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var \PleaseWork\Workshop\Model\PublisherFactory
     */
    protected $publisherFactory;

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
     * @var SearchResultFactory
     */
    protected $searchResultFactory;

    /**
     * PublisherRepository constructor.
     * @param Publisher $publisherModel
     * @param PublisherResource $publisherResource
     * @param CollectionFactory $collectionFactory
     * @param \PleaseWork\Workshop\Model\PublisherFactory $publisherFactory
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultFactory $searchResultFactory
     */
    public function __construct(
        PublisherModel                  $publisherModel,
        PublisherResource               $publisherResource,
        CollectionFactory               $collectionFactory,
        PublisherFactory                $publisherFactory,
        FilterBuilder                   $filterBuilder,
        SearchCriteriaBuilder           $searchCriteriaBuilder,
        CollectionProcessorInterface    $collectionProcessor,
        SearchResultFactory             $searchResultFactory
    ) {
        $this->resourceModel            = $publisherResource;
        $this->model                    = $publisherModel;
        $this->publisherFactory         = $publisherFactory;
        $this->collection               = $collectionFactory;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->filterBuilder            = $filterBuilder;
        $this->collectionProcessor      = $collectionProcessor;
        $this->searchResultFactory      = $searchResultFactory;
    }

    /**
     * @param $objectModel
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function save($objectModel)
    {
        $this->resourceModel->save($objectModel);
    }

    /**
     * @param $model
     * @param $id
     * @throws \Exception
     */
    public function delete($model, $id)
    {
        $model = $this->publisherFactory->create();
        $this->resourceModel->load($model, $id);
        $this->resourceModel->delete($model);
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        return $this->collection->create();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        $publisher = $this->publisherFactory->create();
        $this->resourceModel->load($publisher, $id);
        return $publisher;
    }

    /**
     * @return mixed
     */
    public function createObject()
    {
        return $this->publisherFactory->create();
    }

    /**
     * @param $field
     * @param $param
     * @param $value
     * @return mixed
     */
    public function getFilterList($field, $param, $value)
    {
        return $this->collection->create()
                                ->addFieldTiFilter($field, $param, $value)
                                ->load();
    }
}
