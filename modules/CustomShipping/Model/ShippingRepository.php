<?php

namespace Sandftae\CustomShipping\Model;

use Sandftae\CustomShipping\Api\ShippingRepositoryInterface;
use Sandftae\CustomShipping\Model\ResourceModel\Shipping as ShippingResource;
use Sandftae\CustomShipping\Model\ResourceModel\Shipping\CollectionFactory;

/**
 * Class ShippingRepository
 *
 * @package Sandftae\CustomShipping\Model
 */
class ShippingRepository implements ShippingRepositoryInterface
{
    /**
     * @var ShippingFactory
     */
    protected $shipping;

    /**
     * @var ShippingResource
     */
    protected $shippingResource;

    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * ShippingRepository constructor.
     *
     * @param ShippingFactory   $shipping
     * @param ShippingResource  $shippingResource
     * @param CollectionFactory $collection
     */
    public function __construct(
        ShippingFactory     $shipping,
        ShippingResource    $shippingResource,
        CollectionFactory   $collection
    ) {
        $this->shipping         = $shipping;
        $this->shippingResource = $shippingResource;
        $this->collection       = $collection;
    }

    /**
     * @return array
     */
    public function getData():array
    {
        $collection = $this->collection->create()->load();
        $data = [];

        foreach ($this->generator($collection) as $keys => $items) {
            $data[] = $items->getData();
        }
        return $data;
    }

    /**
     * @param array $dataToSave
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save($dataToSave):void
    {
        $structureTable = $this->getDescribeTable();

        foreach ($dataToSave as $fieldsScope => $fields) {
            $shippingModel = $this->shipping->create();
            foreach ($fields as $keyFiled => $field) {
                $shippingModel->setData($structureTable[$keyFiled], $field);
            }
            $this->shippingResource->save($shippingModel);
        }
    }

    /**
     * save duplicate field values ​​and return unique fields
     *
     * @param array     $fieldsToUpdate
     * @return array
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function updateDuplicateEntries(array $fieldsToUpdate):array
    {
        $collectionCountry = $this->collection->create()->load()->getItems();
        foreach ($this->generator($collectionCountry) as $keys => $countryData) {
            foreach ($this->generator($fieldsToUpdate) as $key => $fieldsScope) {
                if ($countryData->getAbbr() === $fieldsScope[2]) {
                    $countryData->setPrice($fieldsScope[1]);
                    $this->shippingResource->save($countryData);

                    $fieldsToUpdate = array_filter(
                        $fieldsToUpdate,
                        function ($fields) use ($fieldsScope) {
                            return (!($fields === $fieldsScope));
                        }
                    );
                }
            }
        }
        return $fieldsToUpdate;
    }

    /**
     * @param string $abbr
     * @return mixed|null
     */
    public function getCountry(string $abbr)
    {
        $collectionCountry = $this->collection->create()->load()->getItems();
        foreach ($this->generator($collectionCountry) as $keys => $countryData) {
            if ($countryData->getAbbr() === $abbr) {
                return $countryData;
            }
        }
        return null;
    }

    /**
     * Get table structures
     *
     * @return  array
     * @throws  \Magento\Framework\Exception\LocalizedException
     */
    public function getDescribeTable(): array
    {
        $tableName = $this->shippingResource->getMainTable();
        $describeTableData = $this->shippingResource->getConnection()->describeTable($tableName);
        array_shift($describeTableData);
        return array_keys($describeTableData);
    }

    /**
     * @param   $data
     * @return \Generator
     */
    public function generator($data):\Generator
    {
        foreach ($data as $key => $value) {
            yield $key => $value;
        }
    }
}
