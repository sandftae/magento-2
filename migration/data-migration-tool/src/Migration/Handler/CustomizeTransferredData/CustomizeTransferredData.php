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


namespace Migration\Handler\CustomizeTransferredData;

use Migration\Handler\HandlerInterface;
use Migration\Handler\AbstractHandler;
use Migration\ResourceModel\Record;

class CustomizeTransferredData extends AbstractHandler implements HandlerInterface
{
    private $postFix;

    public function __construct($postFix)
    {
        $this->postFix = $postFix;
    }

    public function handle(Record $recordToHandle, Record $oppositeRecord)
    {
        $this->validate($oppositeRecord);
        $value = $recordToHandle->getValue($this->field) . $this->postFix;
        $recordToHandle->setValue($this->field, $value);
    }

}