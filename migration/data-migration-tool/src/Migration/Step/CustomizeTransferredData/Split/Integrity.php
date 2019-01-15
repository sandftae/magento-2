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

namespace Migration\Step\CustomizeTransferredData\Split;


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


/**
 * Class Integrity
 * @package Migration\Step\CustomizeTransferredData\Split
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
        $this->splitMap         = $splitFactory->create('split_file');
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
        $fromTransfer       = $this->splitMap->getFromDocumentsTransfer()[0];
        $listToTrafansfer   = $this->splitMap->getListToDocumentTransfer();
        $source             = $this->source->getDocument($fromTransfer);

        /** The amount of data transferred is always one */
        $this->logLvlProcessor->start(count($listToTrafansfer) + 1);

        foreach ($listToTrafansfer as $docName) {
            $destination = $this->destination->getDocument($docName);
            $this->verifyFields($source, $destination);
        }
        $this->verifyDocEquality($source, $listToTrafansfer);
        $this->logLvlProcessor->finish();

        return $this->checkForErrors();
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
                $this->logger->warning(sprintf(
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
     * @param Document $source
     * @param array $destinations
     */
    public function verifyDocEquality(Document $source, array $destinations):void
    {
        $sourceDoc      = array_keys($source->getStructure()->getFields());
        $destGrandTtl   = [];

        foreach ($destinations as $destDoc) {
            $doc        = $this->destination->getDocument($destDoc);
            $docName    = $doc->getName();
            $fields     = array_keys($doc->getStructure()->getFields());

            foreach($fields as $field){
                $destGrandTtl[] = $field;

                if(!in_array($field, $sourceDoc)
                    && !$this->splitMap->isFieldIgnored($docName, $field, SplitInterface::TYPE_IGNORE, true)
                    && !$this->splitMap->isFieldMoved($docName, $field, SplitInterface::TYPE_MOVE, true)
                ){
                    $this->logger->addRecord(Logger::WARNING, sprintf(
                            'Destination fields are missing. Document: %s. Fields: %s',
                            $docName,
                            $field)
                    );

                    $this->errorEguals = true;
                }
            }
        }

        foreach($sourceDoc as $field){
            if(!in_array($field, $destGrandTtl)
                && !$this->splitMap->isFieldIgnored($source->getName(), $field, SplitInterface::TYPE_IGNORE, false)
                && !$this->splitMap->isFieldMoved($source->getName(), $field, SplitInterface::TYPE_MOVE, true)
            ){
                $this->logger->addRecord(Logger::WARNING, sprintf(
                    'Source fields are missing. Document: %s. Fields: %s',
                    $source->getName(),
                    $field
                ));
                $this->errorEguals = true;
            }
        }
    }

    /**
     * @return bool
     */
    public function checkForErrors():bool
    {
        return ($this->errorTypeHandler || $this->errorEguals) ? false : true;
    }
}