<?php
namespace Sandftae\Donation\Model\Order\Invoice\Total;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Total\AbstractTotal;

/**
 * Class Donation
 * @package Sandftae\Donation\Model\Order\Invoice\Total
 */
class Donation extends AbstractTotal
{
    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getId() && $order->getGrandTotal() && $order->getDonation()) {
            $invoice->setDonation($order->getDonation());
            $invoice->setGrandTotal($order->getGrandTotal());
            $invoice->setBaseGrandTotal($order->getBaseGrandTotal());
        }

        return $this;
    }
}
