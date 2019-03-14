<?php

namespace Sandftae\Donation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class DonationToOrder
 * @package Sandftae\Donation\Observer
 */
class DonationToOrder implements ObserverInterface
{
    /**
     * Set donation to order.
     *
     * @param Observer $observer
     * @throws CouldNotSaveException
     */
    public function execute(Observer $observer)
    {
        try {
            $quote = $observer->getEvent()->getQuote();
            $order = $observer->getEvent()->getOrder();

            if ($quote->getId() && $order->getIncrementId()) {
                if (!$order->getDonation() && $quote->getDonation()) {
                    $order->setDonation($quote->getDonation());
                }
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save donation to order'));
        }
    }
}
