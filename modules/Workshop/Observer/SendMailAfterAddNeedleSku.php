<?php

namespace PleaseWork\Workshop\Observer;

use Magento\Framework\Event\ObserverInterface;
use PleaseWork\Workshop\Helper\Email;
use PleaseWork\Workshop\Block\GetStockConfig;
use Magento\Framework\Event\Observer;

/**
 * Class SendMailAfterAddNeedleSku
 * @package PleaseWork\Workshop\Observer
 */
class SendMailAfterAddNeedleSku implements ObserverInterface
{
    /**
     * @var Email
     */
    private $helperEmail;

    /**
     * @var GetStockConfig
     */
    protected $stockConfig;

    /**
     * SendMailAfterAddNeedleSku constructor.
     * @param Email $helperEmail
     * @param GetStockConfig $stockConfig
     */
    public function __construct(
        Email $helperEmail,
        GetStockConfig $stockConfig
    ) {
        $this->stockConfig = $stockConfig;
        $this->helperEmail = $helperEmail;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $allOrders = $observer->getEvent()->getOrder();

        $this->checkStock($allOrders);
    }

    /**
     * @param $allOrders
     *
     * return void
     */
    protected function checkStock($allOrders)
    {
        $sku = $this->stockConfig
            ->getScopeShow()
            ->getValue(GetStockConfig::PRODUCT_SKU_CONFIG);

        $priceSkuStock = $this->stockConfig
                        ->getScopeShow()
                        ->getValue(GetStockConfig::PRODUCT_PRICE_CONFIG);


        foreach ($allOrders->getItems() as $item) {
            if ($item->getData('sku') == $sku
                and (int) $item->getData('price') == $priceSkuStock) {
                return $this->helperEmail->sendEmail();
            }
        }
    }
}
