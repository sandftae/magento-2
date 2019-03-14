<?php

namespace Sandftae\CustomShipping\Api;

use Sandftae\CustomShipping\Api\General\GeneralGetterInterface;
use Magento\Framework\App\ResponseInterface;
use Sandftae\CustomShipping\Api\General\GeneratorInterface;

/**
 * Interface ExportInterface
 * @package Sandftae\CustomShipping\Api
 */
interface ExportInterface extends GeneralGetterInterface, GeneratorInterface
{
    /**
     * @return ResponseInterface
     */
    public function getCsvFile():ResponseInterface;

    /**
     * @return ExportInterface
     */
    public function createCsvFile():self;
}
