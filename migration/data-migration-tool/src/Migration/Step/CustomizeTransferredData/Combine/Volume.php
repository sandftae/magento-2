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

use Migration\App\Step\AbstractVolume;
use Migration\Reader\SplitFactory;
use Migration\ResourceModel\Source;
use Migration\ResourceModel\Destination;
use Migration\Logger\Logger;
use Migration\App\ProgressBar\LogLevelProcessor;
use Migration\Logger\Manager as LogManager;


/**
 * Class Volume
 * @package Migration\Step\CustomizeTransferredData\Combine
 */
class Volume extends AbstractVolume
{
    /**
     * @var mixed
     */
    private $splitMap;

    /**
     * @var Source
     */
    private $source;

    /**
     * @var Destination
     */
    private $destination;

    /**
     * @var LogLevelProcessor
     */
    private $logLvlProcessor;

    /**
     * Volume constructor.
     * @param Logger $logger
     * @param SplitFactory $splitFactory
     * @param Source $source
     * @param Destination $destination
     * @param LogLevelProcessor $logLevelProcessor
     */
    public function __construct(
        Logger              $logger,
        SplitFactory        $splitFactory,
        Source              $source,
        Destination         $destination,
        LogLevelProcessor   $logLevelProcessor
    ) {
        $this->logLvlProcessor = $logLevelProcessor;
        $this->splitMap        = $splitFactory->create('combine_file');
        $this->destination     = $destination;
        $this->source          = $source;

        parent::__construct($logger);
    }

    /**
     * @return bool
     */
    public function perform():bool
    {
        $listFromTransfer   = $this->splitMap->getFromDocumentsTransfer();
        $listToTransfer     = $this->splitMap->getListToDocumentTransfer();
        $qtyFieldsDestDoc   = (int) $this->destination->getRecordsCount($listToTransfer[0], false);

        $this->logLvlProcessor->start(1,LogManager::LOG_LEVEL_INFO);

        foreach ($listFromTransfer as $doc){
            $docName = $this->source->getDocument($doc)->getName();
            $qtyFieldsSourceDoc = (int) $this->source->getRecordsCount($doc, false);

            if($qtyFieldsDestDoc != $qtyFieldsSourceDoc){
                $this->errors[] = sprintf(
                    'Mismatch of entities in the document: %s Source: %s Destination: %s',
                    $docName,
                    $qtyFieldsSourceDoc,
                    $qtyFieldsDestDoc
                );
            }
            $this->logLvlProcessor->finish(LogManager::LOG_LEVEL_INFO);
        }
        return $this->checkForErrors();
    }
}