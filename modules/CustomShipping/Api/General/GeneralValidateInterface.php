<?php

namespace Sandftae\CustomShipping\Api\General;

/**
 * Interface GeneralValidateInterface
 * @package Sandftae\CustomShipping\Api\General
 */
interface GeneralValidateInterface
{
    /**
     * @param array $file
     * @return mixed
     */
    public function validate(array $file);
}
