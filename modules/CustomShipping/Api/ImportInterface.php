<?php

namespace Sandftae\CustomShipping\Api;

use Sandftae\CustomShipping\Api\General\GeneralValidateInterface;
use Sandftae\CustomShipping\Api\General\GeneratorInterface;

/**
 * Interface ImportInterface
 * @package Sandftae\CustomShipping\Api
 */
interface ImportInterface extends GeneralValidateInterface, GeneratorInterface
{
    /**
     * @return mixed
     */
    public function setMessage();

    /**
     * @param array $dataToUpdate
     * @return array
     */
    public function rewriteDuplicateEntries(array $dataToUpdate):array;
}
