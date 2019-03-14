<?php

namespace Sandftae\CustomShipping\Model\Import;

use Magento\Config\Model\Config\Backend\File;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Framework\Message\ManagerInterface;
use Sandftae\CustomShipping\Api\ShippingRepositoryInterface;
use Sandftae\CustomShipping\Api\ImportInterface;

/**
 * Class Import
 *
 * @package Sandftae\CustomShipping\Model\Import
 */
class Import extends File implements ImportInterface
{
    /**
     * directory to save
     */
    const UPLOAD_DIR = 'catalog/uploaded/csv';

    /**
     * default checked position
     */
    const DEFAULT_REWRITE_POSITION = 'yes-rewrite';

    /**
     * @var Csv
     */
    protected $csv;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ShippingRepositoryInterface
     */
    protected $shippingRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Import constructor.
     *
     * @param Context                       $context
     * @param Registry                      $registry
     * @param ScopeConfigInterface          $config
     * @param TypeListInterface             $cacheTypeList
     * @param UploaderFactory               $uploaderFactory
     * @param RequestDataInterface          $requestData
     * @param Filesystem                    $filesystem
     * @param Csv                           $csv
     * @param ManagerInterface              $messageManager
     * @param ShippingRepositoryInterface   $shippingRepository
     * @param AbstractResource|null         $resource
     * @param AbstractDb|null               $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context                     $context,
        Registry                    $registry,
        ScopeConfigInterface        $config,
        TypeListInterface           $cacheTypeList,
        UploaderFactory             $uploaderFactory,
        RequestDataInterface        $requestData,
        Filesystem                  $filesystem,
        Csv                         $csv,
        ManagerInterface            $messageManager,
        ShippingRepositoryInterface $shippingRepository,
        AbstractResource            $resource = null,
        AbstractDb                  $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $uploaderFactory,
            $requestData,
            $filesystem,
            $resource,
            $resourceCollection,
            $data
        );

        $this->scopeConfig        = $config;
        $this->csv                = $csv;
        $this->messageManager     = $messageManager;
        $this->shippingRepository = $shippingRepository;
    }

    /**
     * Validates the download file
     *
     * @param   $file
     * @return  mixed|void
     * @throws  \Exception
     */
    public function validate(array $file)
    {
        $fullName = $file['name'];

        $hasCsv = stristr(
            $fullName,
            $this->_getAllowedExtensions()[0]

        );

        // check file type
        if (!$hasCsv) {
            throw new \Exception($this->setMessage());
        }

        $csvData = $this->csv->getData($file['tmp_name']);

        // make sure the file is not empty
        if (count($csvData) <= 1) {
            throw new \Exception($this->setMessage());
        }

        // check file structure
        $dataToSave = $this->checkFieldEquality($csvData);

        // If the "Rewrite the records" checkbox is selected as "Yes" -
        // write down the records instead of the records in the database.
        if ($this->getRewritePosition() === self::DEFAULT_REWRITE_POSITION) {
            $dataToSave = $this->rewriteDuplicateEntries($dataToSave);
        }

        $this->shippingRepository->save($dataToSave);
    }

    /**
     * @param array $dataToUpdate
     * @return array
     */
    public function rewriteDuplicateEntries(array $dataToUpdate):array
    {
        return $this->shippingRepository->updateDuplicateEntries($dataToUpdate);
    }

    /**
     * @return string
     */
    public function getRewritePosition():string
    {
        return $this->scopeConfig->getValue($this->getConfigPath());
    }

    /**
     * Check the number of fields in the uploaded file
     *
     * @param   array $fieldsScope
     * @return  array
     * @throws          \Exception
     */
    protected function checkFieldEquality(array $fieldsScope): array
    {
        $tableStructure = $this->getDescribeTable();
        $fieldsScope = $this->prepareDataToCompare($fieldsScope);

        $hasDiffStructures = array_diff($tableStructure, $fieldsScope[0]);

        if (!empty($hasDiffStructures)) {
            throw new \Exception($this->setMessage());
        }

        // delete first field - description of table structure
        array_shift($fieldsScope);
        $qtyFieldsInStructure = count($tableStructure);

        // compare the number of fields in the csv file,
        // with the possible number of fields to write
        foreach ($this->generator($fieldsScope) as $fields => $field) {
            if ($qtyFieldsInStructure != count($field)) {
                throw new \Exception($this->setMessage());
            }
            foreach ($field as $valueField) {
                if (empty($valueField)) {
                    throw new \Exception($this->setMessage());
                }
            }
        }
        return $fieldsScope;
    }

    /**
     * Remove spaces
     *
     * @param   $data
     * @return  array
     */
    protected function prepareDataToCompare($data): array
    {
        $dataToReturned = [];
        foreach ($this->generator($data) as $key => $value) {
            if (is_array($value)) {
                $dataToReturned[$key] = $this->prepareDataToCompare($value);
                continue;
            }
            $dataToReturned[$key] = trim($value);
        }
        return $dataToReturned;
    }

    /**
     * Get table structures
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getDescribeTable(): array
    {
        return $this->shippingRepository->getDescribeTable();
    }

    /**
     * @param   array $data
     * @return \Generator
     */
    public function generator(array $data): \Generator
    {
        foreach ($data as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Get error message
     *
     * @return string
     */
    public function setMessage(): string
    {
        return 'Invalid CSV file. Check the file type and the equality of
                the structure of the loaded file and the table in the database';
    }

    /**
     * @return string
     */
    protected function getConfigPath():string
    {
        return 'carriers/simpleshipping/alignment';
    }
    /**
     * Get downloaded directory
     *
     * @return string
     */
    protected function _getUploadDir(): string
    {
        return $this->_mediaDirectory->getAbsolutePath(self::UPLOAD_DIR);
    }

    /**
     * @return bool
     */
    protected function _addWhetherScopeInfo(): bool
    {
        return true;
    }

    /**
     * Get type of downloaded the file
     *
     * @return array
     */
    protected function _getAllowedExtensions(): array
    {
        return ['csv'];
    }
}
