<?php

namespace Migration\Reader;

use Magento\Framework\App\Arguments\ValidationState;
use Migration\App\Api\Split\SplitInterface;
use Migration\Exception;


/**
 * Class Split
 * @package Migration\Reader
 */
class Split implements SplitInterface
{
    const CONFIGURATION_SPLIT_SCHEMA = 'split_combine_process.xsd';

    /**
     * @var ValidationState
     */
    protected $validationState;

    /**
     * Split constructor.
     * @param ValidationState $validationState
     * @param $mapFile
     * @throws Exception
     */
    public function __construct(
        ValidationState $validationState,
        $mapFile
    ) {
        $this->validationState = $validationState;
        $this->init($mapFile);
    }

    /**
     * @param $mapFile
     * @return $this
     * @throws Exception
     */
    protected function init($mapFile):SplitInterface
    {
        $this->ignoredDocuments = [];
        $this->wildcards = null;

        $configFile = $this->getRootDir() . $mapFile;
        if (!is_file($configFile)) {
            throw new Exception('Invalid split filename: ' . $configFile);
        }

        $xml = file_get_contents($configFile);
        $document = new \Magento\Framework\Config\Dom($xml, $this->validationState);

        if (!$document->validate($this->getRootDir() .'etc/' . self::CONFIGURATION_SPLIT_SCHEMA)) {
            throw new Exception('XML file is invalid.');
        }

        $this->xml = new \DOMXPath($document->getDom());
        return $this;
    }

    /**
     * @return string
     */
    protected function getRootDir():string
    {
        return dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $document
     * @param string $field
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public function isFieldIgnored(string $document, string $field, string $type, $parentTag = false):bool
    {
        $parentTag = $parentTag ? 'destination' : 'source';

        $this->validateAmbiguity($document, $field);

        $map = $this->xml->query(sprintf('//settings/' . $parentTag . '/' . $type . '/field[text()="%s.%s"]', $document, $field));

        return ($map->length > 0);
    }

    /**
     * @param string $document
     * @param string $field
     * @return bool
     */
    public function isFieldDataTypeIgnored(string $document, string $field):bool
    {
        $map = $this->xml->query(sprintf('//settings/destination/ignore/datatype[text()="%s.%s"]', $document, $field));

        return ($map->length > 0);
    }

    /**
     * @param $document
     * @param $field
     * @param $type
     * @return bool
     * @throws Exception
     */
    public function validateAmbiguity(string $document, string $field):bool
    {
        $move           = $this->xml->query(sprintf('//settings/source/move/field[text()="%s.%s"]', $document, $field));
        $moveTo         = $this->xml->query(sprintf('//settings/source/move/to[text()="%s.%s"]', $document, $field));
        $ignoreSource   = $this->xml->query(sprintf('//settings/source/ignore/field[text()="%s.%s"]', $document, $field));
        $ignoreDest     = $this->xml->query(sprintf('//settings/destination/ignore/field[text()="%s.%s"]', $document, $field));

        if(($move->length > 0 && $ignoreSource->length > 0)
            || ($moveTo->length > 0 && $ignoreDest->length > 0)) {
            throw new Exception('Field has ambiguous configuration. Table: ' . $document.'. Field: ' . $field);
        }
        return true;
    }

    /**
     * @param bool $modeCombine
     * @return array|mixed|string
     */
   public function getFromDocumentsTransfer($modeCombine = false)
   {
       $nodes = $this->xml->query('//from/documents/document');

       if($modeCombine){
           $nodes = $this->xml->query('//from/documents/document');
       }

       foreach ($nodes as $node) {
            $result[] = trim($node->nodeValue);
       }

       if(!empty($result)){
           return $result;
       }

       $result = trim($node->nodeValue);

       return $result;
   }

    /**
     * @return array
     */
   public function getListToDocumentTransfer():array
   {
       $result = [];
       $nodes = $this->xml->query('//transfer/document');

       foreach ($nodes as $node) {
           $result[] = trim($node->nodeValue);
       }
       return $result;
   }

    /**
     * @return array
     */
    public function getForeignKeyList():array
    {
         $result = [];
        $nodes = $this->xml->query('//settings/destination/foreign_key');

        if($nodes->length > 0){
            $primary = $this->xml->query('//settings/destination/foreign_key/to_that');
            $foreign = $this->xml->query('//settings/destination/foreign_key/this');

            $primKey = explode('.', $primary->item(0)->nodeValue);

            $result['primaryDoc'] = $primKey[0];
            $result['primaryField'] = $primKey[1];

            $forKey = explode('.', trim($foreign->item(0)->nodeValue));

            $result['foreignDoc'] = $forKey[0];
            $result['foreignField'] = $forKey[1];
        }
        return $result;
    }

    /**
     * @param string $document
     * @param string $field
     * @param string $type
     * @return array|bool
     * @throws Exception
     */
    public function isFieldMoved(string $document, string $field, string $type, $mode = false)
    {
        $result = [];
        $this->validateAmbiguity($document, $field);

        if($mode){
            $mapDestFields = $this->xml->query(sprintf('//settings/source/' . $type . '/to[text()="%s.%s"]', $document, $field));
            $mapSourceFields = $this->xml->query(sprintf('//settings/source/' . $type . '/field[text()="%s.%s"]', $document, $field));

            return ($mapDestFields->length > 0 || $mapSourceFields->length > 0) ? true : false;
        }

        $map = $this->xml->query(sprintf('//settings/source/' . $type . '/field[text()="%s.%s"]', $document, $field));
        if($map->length == 0) {
            return false;
        }

        $toField = $this->xml->query('//settings/source/' . $type . '/to');

        foreach ($toField as $node) {
            $result[] = explode('.', trim($node->nodeValue));
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getFieldsDatatypeIgnored():array
    {
        $result = [];

        $map = $this->xml->query('//settings/destination/ignore/datatype');

        foreach ($map as $node) {
            $result[] = explode('.', trim($node->nodeValue));
        }
        return $result;
    }
}