<?php

namespace Migration\App\Api\Split;

use Migration\App\Api\General\GeneralInterface;

/**
 * Interface SplitInterface
 * @package Migration\App\Api\Split
 */
interface SplitInterface extends GeneralInterface
{
    const TYPE_IGNORE = 'ignore';
    const TYPE_MOVE = 'move';

    /**
     * @return array
     */
    public function getListToDocumentTransfer():array;

    /**
     * @return mixed
     */
    public function isFieldDataTypeIgnored(string $document, string $field);

    /**
     * @return mixed
     */
    public function getFromDocumentsTransfer();

}