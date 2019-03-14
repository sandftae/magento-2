<?php

namespace Sandftae\CustomShipping\Api;

/**
 * Interface ShippingFrontValidateInterface
 *
 * @package Sandftae\CustomShipping\Api
 */
interface ShippingFrontValidateInterface
{
    /**
     * @param null $country
     * @return float|mixed
     */
    public function getShippingPrice($country = null);
}
