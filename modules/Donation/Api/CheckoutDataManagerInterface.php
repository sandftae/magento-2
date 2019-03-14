<?php

namespace Sandftae\Donation\Api;

/**
 * Interface CheckoutDataManagerInterface
 * @package Sandftae\Donation\Api
 */
interface CheckoutDataManagerInterface
{

    /**
     * Set donation to quote.
     *
     * @param string $donationCost
     * @param string $cartId
     *
     * @return mixed
     */
    public function setDonation($donationCost, $cartId);

    /**
     * Remove donation from quote.
     *
     * @param string $cartId
     *
     * @return mixed
     */
    public function removeDonation($cartId);
}
