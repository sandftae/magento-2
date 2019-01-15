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
 * @package Magecom_Extension
 * @copyright Copyright (c)  2019 Magecom, Inc. (http://www.magecom.net)
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Migration\Step\CustomizeTransferredData\Split;

use Migration\App\Step\StageInterface;
use Migration\ResourceModel\Document;
use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;
use Migration\ResourceModel\RecordFactory;
use Migration\App\ProgressBar\LogLevelProcessor;
use Migration\Reader\SplitFactory;
use Migration\RecordTransformerFactory;
use Migration\Logger\Manager as LogManager;
use Migration\Logger\Logger;
use Migration\App\Progress;


/**
 * Class Data
 * @package Migration\Step\CustomizeTransferredData\Split
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
        $this->splitMap                 = $splitFactory->create('split_file');
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
     * @throws \Migration\Exception
     */
    public function perform():bool
    {
        $recordsDest = [];
        $listDestDocuments = [];
        $pageNumber = 0;
        $destinationRecords = null;

        $fromTransfer       = $this->splitMap->getFromDocumentsTransfer()[0];
        $listToTrafansfer   = $this->splitMap->getListToDocumentTransfer();
        $sourceDocument     = $this->sourceModel->getDocument($fromTransfer);
        
        while (!empty($items = $this->sourceModel->getRecords($fromTransfer, $pageNumber))) {
            foreach ($items as $key => $dataItems) {
                $count = $this->sourceModel->getRecordsCount($fromTransfer);
                $this->logLevelProcessor->start(1, LogManager::LOG_LEVEL_INFO);

               $recordSource = $this->recordFactory->create(['document' => $sourceDocument, 'data' => $dataItems]);

               foreach ($listToTrafansfer as $tableToTransferer) {

                   $destDocument        = $this->destinationModel->getDocument($tableToTransferer);
                   $recordTransformer   = $this->getRecordTransformer($destDocument, $sourceDocument);
                   $recordsDest         = $this->recordFactory->create(['document' => $destDocument]);
                   $docName             = $recordsDest->getDocument()->getName();

                   $recordTransformer->divide($recordSource, $recordsDest);

                   $listDestDocuments[$docName][] = $recordsDest;
               }
                $this->logLevelProcessor->finish(LogManager::LOG_LEVEL_INFO);
            }
           $pageNumber++;
       }

       foreach ($listDestDocuments as $destDocName => $destDocRecords) {
           $destDocument        = $this->destinationModel->getDocument($destDocName);
           $destinationRecords  = $destDocument->getRecords();

           foreach ($destDocRecords as $destDocRecord){
               $destinationRecords->addRecord($destDocRecord);
           }
           $this->destinationModel->saveRecords($destDocName, $destinationRecords);
       }
        $this->logLevelProcessor->finish(LogManager::LOG_LEVEL_INFO);
       return true;
    }

    /**
     * @param Document $sourceDocument
     * @param Document $destDocument
     * @return \Migration\RecordTransformer
     */
    public function getRecordTransformer(Document $sourceDocument, Document $destDocument):\Migration\RecordTransformer
    {
        $recordTransformer = $this->recordTransformerFactory->create(
            [
                'sourceDocument' => $sourceDocument,
                'destDocument'   => $destDocument,
                'mapReader'      => $this->splitMap
            ]
        );
        $recordTransformer->init();
        return $recordTransformer;
    }


}