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

namespace Migration\App\Api\General;

/**
 * Interface GeneralInterface
 * @package Migration\App\Api\General
 */
interface GeneralInterface
{
    /**
     * Is field ignored
     *
     * @param string $document
     * @param string $field
     * @param string $type
     * @return mixed
     */
    public function isFieldIgnored(string $document, string $field, string $type);
}