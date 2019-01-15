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

use Migration\App\Api\Split\SplitInterface;
use Migration\App\Step\StageInterface;
use Migration\ResourceModel\Document;
use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;
use Migration\Reader\SplitFactory;
use Migration\Config;
use Migration\Logger\Logger;
use Migration\ResourceModel\RecordFactory;
use Migration\App\ProgressBar\LogLevelProcessor;
use Migration\Exception;
use Migration\Logger\Manager as LogManager;

/**
 * Class Integrity
 * @package Migration\Step\CustomizeTransferredData\Combine
 */
class Integrity implements StageInterface
{
    /**
     * @var mixed
     */
    private $splitMap;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var Destination
     */
    private $destination;

    /**
     * @var Source
     */
    private $source;

    /**
     * @var RecordFactory
     */
    private $recordFactory;

    /**
     * @var $errorTypeHandler
     */
    private $errorTypeHandler;

    /**
     * @var $errorEguals
     */
    private $errorEguals;

    /**
     * @var LogLevelProcessor
     */
    private $logLvlProcessor;

    /**
     * Integrity constructor.
     * @param SplitFactory $splitFactory
     * @param Logger $logger
     * @param Destination $destination
     * @param Source $source
     * @param RecordFactory $recordFactory
     */
    public function __construct
    (
        SplitFactory        $splitFactory,
        Logger              $logger,
        Destination         $destination,
        Source              $source,
        RecordFactory       $recordFactory,
        LogLevelProcessor   $logLevelProcessor
    ) {
        $this->splitMap         = $splitFactory->create('combine_file');
        $this->logger           = $logger;
        $this->destination      = $destination;
        $this->source           = $source;
        $this->recordFactory    = $recordFactory;
        $this->logLvlProcessor  = $logLevelProcessor;
    }

    /**
     * @return bool
     */
    public function perform():bool
    {
        $listFromTransfer = $this->splitMap->getFromDocumentsTransfer();
        $listToTrafansfer = $this->splitMap->getListToDocumentTransfer();
        $deestinationDoc  = $this->destination->getDocument($listToTrafansfer[0]);

        $this->logLvlProcessor->start(1,LogManager::LOG_LEVEL_INFO);

        foreach ($listFromTransfer as $document){
            $sourceDoc = $this->source->getDocument($document);
            $this->verifyFields($sourceDoc, $deestinationDoc);

            $this->logLvlProcessor->finish(LogManager::LOG_LEVEL_INFO);
        }
        return $this->checkErrors();
    }

    /**
     * @param Document $from
     * @param Document $to
     */
    public function verifyFields(Document $from, Document $to):void
    {
        $sourceFileds   = $from->getStructure()->getFields();
        $destFileds     = $to->getStructure()->getFields();
        $compared       = array_intersect_key($destFileds, $sourceFileds);
        $destDocName    = $to->getName();

        foreach ($compared as $fieldName => $fieldStructure) {
            if (
                ($sourceFileds[$fieldName]['DATA_TYPE'] != $destFileds[$fieldName]['DATA_TYPE']) &&
                !$this->splitMap->isFieldDataTypeIgnored($to->getName(), $fieldName)
            ) {
                $this->logger->error(sprintf(
                        'Mismatch of data types. Destination document: %s. Fields: %s. Data type in source: %s. Data type in destionation: %s',
                        $to->getName(),
                        $fieldName,
                        $sourceFileds[$fieldName]['DATA_TYPE'],
                        $destFileds[$fieldName]['DATA_TYPE']
                    )
                );
                $this->errorHandler = true;
            }
        }
    }

    /**
     * @return bool
     */
    public function checkErrors():bool
    {
        return ($this->errorTypeHandler || $this->errorEguals) ? false : true;
    }
}