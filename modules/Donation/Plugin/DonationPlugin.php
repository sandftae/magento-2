<?php

namespace Sandftae\Donation\Plugin;

use Sandftae\Donation\Model\DonationConfigProvider;
use Magento\Framework\DataObject;
use Magento\Sales\Block\Order\Totals;

/**
 * Class DonationPlugin
 * @package Sandftae\Donation\Plugin
 */
class DonationPlugin
{
    /**
     * @var DonationConfigProvider
     */
    private $checkoutConfigProvider;

    /**
     * DonationPlugin constructor.
     * @param DonationConfigProvider $checkoutConfigProvider
     */
    public function __construct(
        DonationConfigProvider $checkoutConfigProvider
    ) {
        $this->checkoutConfigProvider = $checkoutConfigProvider;
    }

    /**
     * Add new total for renderer to order_view and invoice_view.
     *
     * @param Totals $subject
     *
     * @return mixed
     */
    public function beforeGetTotals(
        Totals $subject,
        $area = null
    ) {
        if ($this->checkoutConfigProvider->getModuleEnable() && !$subject->getTotal('donation')) {
            $order = $subject->getOrder();
            if ($order->getId() && $order->getDonation()) {
                $donationTotal = new DataObject([
                    'code' => 'donation',
                    'value' => $order->getDonation(),
                    'label' => __('Donation'),
                ]);

                $subject->addTotal($donationTotal);
            }
        }
        return [$area];
    }
}
