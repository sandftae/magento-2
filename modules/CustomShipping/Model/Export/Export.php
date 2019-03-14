<?php

namespace Sandftae\CustomShipping\Model\Export;

use Sandftae\CustomShipping\Api\ExportInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Response\Http\FileFactory;
use Magecom\CustomShipping\Api\ShippingRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;

/**
 * Class Export
 * @package Sandftae\CustomShipping\Model\Export
 */
class Export implements ExportInterface
{
    /**
     * default name for downloaded CSV file
     */
    const CSV_FILE_NAME = 'total_row_shipping.csv';

    /**
     * @var ShippingRepositoryInterface
     */
    protected $shippingRepository;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * a link to the current stream by which data is written to the file
     *
     * @var $streamCSVfile
     */
    protected $streamCSVfile;

    /**
     * temporary directory in which the recorded file is stored
     *
     * @var $filePath
     */
    protected $filePath;

    /**
     * @var array
     */
    protected $responseContent = [];

    /**
     * Export constructor.
     *
     * @param Filesystem $filesystem
     * @param FileFactory $fileFactory
     * @param ShippingRepositoryInterface $shippingRepository
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        Filesystem                   $filesystem,
        FileFactory                  $fileFactory,
        ShippingRepositoryInterface  $shippingRepository
    ) {
        $this->fileFactory          = $fileFactory;
        $this->shippingRepository   = $shippingRepository;
        $this->directory            = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
    }

    /**
     * @return array
     */
    public function getData():array
    {
        return $this->shippingRepository->getData();
    }

    /**
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getCsvFile():ResponseInterface
    {
        $csv = $this->fileFactory->create(
            self::CSV_FILE_NAME,
            $this->getResponseContent(),
            DirectoryList::VAR_DIR
        );
        return $csv;
    }

    /**
     * @return  ExportInterface
     * @throws  \Magento\Framework\Exception\FileSystemException
     */
    public function createCsvFile():ExportInterface
    {
        $this->filePath = 'export/_' . date('m_d_Y_H_i_s') . '.csv';
        $this->directory->create('export');
        $this->streamCSVfile = $this->directory->openFile($this->filePath, 'w+');
        $this->streamCSVfile->lock();

        return $this;
    }

    /**
     * @return array
     */
    public function getResponseContent():array
    {
        return $this->responseContent;
    }

    /**
     * @return void
     */
    public function setResponseContent():void
    {
        $this->responseContent['type']  = 'filename'; // must keep filename
        $this->responseContent['value'] = $this->filePath;
        $this->responseContent['rm']    = true; //remove csv from var folder
    }

    /**
     * @return  ExportInterface
     * @throws  \Magento\Framework\Exception\LocalizedException
     */
    public function setData():ExportInterface
    {
        $columns = $this->getColumnHeader();

        // set id for first column
        $header[] = 'id';

        foreach ($this->generator($columns) as $key => $column) {
            $header[] = $column;
        }
        // write header
        $this->streamCSVfile->writeCsv($header);

        $allShippingCollection = $this->getData();
        foreach ($this->generator($allShippingCollection) as $keys => $shippingSpecificData) {
            $this->streamCSVfile->writeCsv($shippingSpecificData);
        }
        $this->setResponseContent();

        return $this;
    }

    /**
     * @return  array
     * @throws  \Magento\Framework\Exception\LocalizedException
     */
    public function getColumnHeader():array
    {
        return $this->shippingRepository->getDescribeTable();
    }

    /**
     * @param   array       $data
     * @return  \Generator
     */
    public function generator(array $data): \Generator
    {
        foreach ($data as $key => $value) {
            yield $key => $value;
        }
    }
}
