<?php

namespace Sandftae\CustomShipping\Api;

use Sandftae\CustomShipping\Api\General\GeneralGetterInterface;

/**
 * Interface ShippingRepositoryInterface
 * @package Sandftae\CustomShipping\Api
 */
interface ShippingRepositoryInterface extends GeneralGetterInterface
{
    /**
     * @param array $data
     * @return mixed
     */
    public function save(array $data);

    /**
     * @param array $dataToUpdate
     * @return array
     */
    public function updateDuplicateEntries(array $dataToUpdate):array;
}
