<?php

namespace Sandftae\Donation\Model\Quote\Address\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

/**
 * Class Donation
 * @package Sandftae\Donation\Model\Quote\Address\Total
 */
class Donation extends AbstractTotal
{
    /**
     * Add grand total information to address
     *
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        return [
            'code' => $this->getCode(),
            'title' => __('Donation'),
            'value' => $quote->getDonation(),
        ];
    }

    /**
     * Collect grand total address amount
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this|AbstractTotal
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        if ($donation = $quote->getDonation()) {
            $total->addTotalAmount('donation', $donation);
            $total->addBaseTotalAmount('donation', $donation);
        }

        return $this;
    }
}
