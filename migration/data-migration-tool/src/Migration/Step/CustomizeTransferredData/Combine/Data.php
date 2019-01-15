<?php
/**
 * Magecom
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magecom.net so we can send you a copy immediately.
 *
 * @category Magecom
 * @package Magecom_Module
 * @copyright Copyright (c)  2019 Magecom, Inc. (http://www.magecom.net)
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Migration\Step\CustomizeTransferredData\Combine;

use Migration\App\Step\StageInterface;
use Migration\ResourceModel\Document;
use Migration\ResourceModel\Record;
use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;
use Migration\ResourceModel\RecordFactory;
use Migration\App\ProgressBar\LogLevelProcessor;
use Migration\Reader\SplitFactory;
use Migration\RecordTransformerFactory;
use Migration\Logger\Manager as LogManager;
use Migration\Logger\Logger;
use Migration\App\Progress;
use Migration\Exception;

/**
 * Class Data
 * @package Migration\Step\CustomizeTransferredData\Combine
 */
class Data implements StageInterface
{
    /**
     * @var Source
     */
    private $sourceModel;

    /**
     * @var Destination
     */
    private $destinationModel;

    /**
     * @var RecordFactory
     */
    private $recordFactory;

    /**
     * @var LogLevelProcessor
     */
    private $logLevelProcessor;

    /**
     * @var mixed
     */
    private $splitMap;

    /**
     * @var RecordTransformerFactory
     */
    private $recordTransformerFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Progress
     */
    private $progress;

    /**
     * Data constructor.
     * @param Source $sourceModel
     * @param Logger $logger
     * @param Destination $destinationModel
     * @param RecordFactory $recordFactory
     * @param LogLevelProcessor $logLevelProcessor
     * @param SplitFactory $splitFactory
     * @param RecordTransformerFactory $recordTransformerFactory
     * @param Progress $progress
     */
    public function __construct
    (
        Source                      $sourceModel,
        Logger                      $logger,
        Destination                 $destinationModel,
        RecordFactory               $recordFactory,
        LogLevelProcessor           $logLevelProcessor,
        SplitFactory                $splitFactory,
        RecordTransformerFactory    $recordTransformerFactory,
        Progress                    $progress
    ) {
        $this->splitMap                 = $splitFactory->create('combine_file');
        $this->destinationModel         = $destinationModel;
        $this->logger                   = $logger;
        $this->sourceModel              = $sourceModel;
        $this->recordFactory            = $recordFactory;
        $this->logLevelProcessor        = $logLevelProcessor;
        $this->recordTransformerFactory = $recordTransformerFactory;
        $this->progress                 = $progress;
    }
    
    /**
     * @return bool
     */
    public function perform():bool
    {
        $fromTransfered      = $this->splitMap->getFromDocumentsTransfer();
        $toTransfer          = $this->splitMap->getListToDocumentTransfer();
        $destDocument        = $this->destinationModel->getDocument($toTransfer[0]);
        $destinationRecords  = $destDocument->getRecords();

        $pageNumber         = 0;
        $grandTotal         = [];

        if(!empty($fromTransfered[0])) {
            while (!empty($this->sourceModel->getRecords($fromTransfered[0], $pageNumber))) {
                $this->logLevelProcessor->start(1, LogManager::LOG_LEVEL_INFO);
                $items = [];

                foreach ($fromTransfered as $key => $value){
                    $items[] = $this->sourceModel->getRecords($fromTransfered[$key], $pageNumber);
                }

                foreach ($this->generator($items[0]) as $keys => $values) {
                    $grandTotal[$keys] = $values;
                    for($i = 1; $i < count($items); $i++){
                        $grandTotal[$keys] += $items[$i][$keys];
                    }
                }

                foreach ($this->generator($grandTotal) as $fields) {
                    $recordsDest = $this->recordFactory->create(['document' => $destDocument]);
                    $this->combine($recordsDest, $fields);
                    $destinationRecords->addRecord($recordsDest);
                }
                $this->logLevelProcessor->finish(LogManager::LOG_LEVEL_INFO);
                $pageNumber++;
            }
        }
        $this->destinationModel->saveRecords($destDocument->getName(), $destinationRecords);
        return true;
    }

    /**
     * @param array $data
     * @return \Generator
     */
    public function generator(array $data):\Generator
    {
        foreach ($data as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @param Record $record
     * @param array $scopeFieldsToSet
     * @throws \Migration\Exception
     */
    public function combine(Record $record, array $fieldsScope):void
    {
        foreach ($this->generator($fieldsScope) as $fieldIndex => $fieldVal) {
            $record->setValue($fieldIndex, $fieldVal);
        }
    }
}