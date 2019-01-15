<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration;

use Migration\App\Api\Split\SplitInterface;
use Migration\Handler\AbstractHandler;
use Migration\Reader\MapInterface;
use Migration\ResourceModel\Record;

use Migration\App\Api\General\GeneralInterface;

/**
 * Class RecordTransformer
 */
class RecordTransformer
{
    /**
     * @var ResourceModel\Document
     */
    protected $sourceDocument;

    /**
     * @var ResourceModel\Document
     */
    protected $destDocument;

    /**
     * @var Handler\ManagerFactory
     */
    protected $handlerManagerFactory;

    /**
     * @var Handler\Manager
     */
    protected $sourceHandlerManager;

    /**
     * @var Handler\Manager
     */
    protected $destHandlerManager;

    /**
     * @var MapInterface
     */
    protected $mapReader;

    /**
     * @param ResourceModel\Document $sourceDocument
     * @param ResourceModel\Document $destDocument
     * @param Handler\ManagerFactory $handlerManagerFactory
     * @param MapInterface $mapReader
     */
    public function __construct(
        ResourceModel\Document  $sourceDocument,
        ResourceModel\Document  $destDocument,
        Handler\ManagerFactory  $handlerManagerFactory,
        GeneralInterface        $mapReader
    ) {
        $this->sourceDocument           = $sourceDocument;
        $this->destDocument             = $destDocument;
        $this->handlerManagerFactory    = $handlerManagerFactory;
        $this->mapReader                = $mapReader;
    }

    /**
     * Transform
     *
     * @param Record $from
     * @param Record $to
     * @return void
     */
    public function transform(Record $from, Record $to)
    {
        $this->applyHandlers($this->sourceHandlerManager, $from, $to);
        $this->copy($from, $to);
        $this->applyHandlers($this->destHandlerManager, $to, $to);
    }

    /**
     * Init
     *
     * @return $this
     */
    public function init()
    {
        if($this->mapReader instanceof SplitInterface){
            return;
        }
        $this->sourceHandlerManager = $this->initHandlerManager(MapInterface::TYPE_SOURCE);
        $this->destHandlerManager = $this->initHandlerManager(MapInterface::TYPE_DEST);
        return $this;
    }

    /**
     * Init handler manager
     *
     * @param string $type
     * @return Handler\Manager
     */
    protected function initHandlerManager($type = MapInterface::TYPE_SOURCE)
    {
        if($this->mapReader instanceof SplitInterface){
            return;
        }
        /** @var ResourceModel\Document $document */
        $document = (MapInterface::TYPE_SOURCE == $type) ? $this->sourceDocument : $this->destDocument;
        /** @var Handler\Manager $handlerManager */
        $handlerManager = $this->handlerManagerFactory->create();
        $fields = $document->getStructure()->getFields();
        foreach (array_keys($fields) as $field) {
            $handlerConfigs = $this->mapReader->getHandlerConfigs($document->getName(), $field, $type);
            foreach ($handlerConfigs as $handlerConfig) {
                $handlerKey = md5($field . $handlerConfig['class']);
                $handlerManager->initHandler($field, $handlerConfig, $handlerKey);
            }
        }
        return $handlerManager;
    }

    /**
     * Apply handlers
     *
     * @param Handler\Manager $handlerManager
     * @param Record $recordToHandle
     * @param Record $oppositeRecord
     * @return void
     */
    public function applyHandlers(Handler\Manager $handlerManager, Record $recordToHandle, Record $oppositeRecord)
    {
        foreach ($handlerManager->getHandlers() as $handler) {
            /** @var $handler AbstractHandler */
            $handler->handle($recordToHandle, $oppositeRecord);
        }
    }

    /**
     * Copy
     *
     * @param Record $from
     * @param Record $to
     * @return void
     */
    protected function copy(Record $from, Record $to)
    {
        $sourceDocumentName = $this->sourceDocument->getName();
        $sourceFields = $from->getFields();
        $sourceFieldsExtra = array_diff($sourceFields, $to->getFields());
        $data = [];
        foreach ($sourceFields as $field) {
            if ($this->mapReader->isFieldIgnored($sourceDocumentName, $field, MapInterface::TYPE_SOURCE)) {
                continue;
            }
            $fieldMap = $this->mapReader->getFieldMap($sourceDocumentName, $field, MapInterface::TYPE_SOURCE);
            if ($fieldMap == $field && in_array($field, $sourceFieldsExtra)) {
                continue;
            }
            $data[$fieldMap] = $from->getValue($field);
        }
        foreach ($data as $key => $value) {
            $to->setValue($key, $value);
        }
    }

    /**
     * @param Record $from
     * @param Record $to
     * @throws Exception
     */
    public function divide(Record $from, Record $to):void
    {
        $sourceDocumentName = $this->destDocument->getName();
        $sourceFields = $from->getFields();
        $this->destName = $to->getDocument()->getName();
        $sourceDestDiff = array_diff($sourceFields, $to->getFields());

        foreach ($this->generator($sourceFields) as $key => $field) {
            if($this->mapReader->isFieldIgnored($sourceDocumentName, $field, SplitInterface::TYPE_IGNORE)){
                continue;
            }

            $fieldsToMove = $this->mapReader->isFieldMoved($sourceDocumentName, $field, SplitInterface::TYPE_MOVE);
            if($fieldsToMove) {
                if($this->getDestDocName() == $fieldsToMove[0][0]){
                    foreach ($fieldsToMove as $fieldMove) {
                        $data[$fieldMove[1]] = $from->getValue($field);
                    }
                    continue;
                }
            }

            if(in_array($field, $sourceDestDiff)) {
                continue;
            }
            $data[$field] =  $from->getValue($field);
        }

        foreach ($data as $key => $value) {
            $to->setValue($key, $value);
        }
    }

    /**
     * @return string
     */
    public function getDestDocName():string
    {
        return $this->destName;
    }

    /**
     * @param array $data
     * @return \Generator
     */
    public function generator(array $data):\Generator
    {
        foreach ($data as $item) {
            yield $item;
        }
    }
}
