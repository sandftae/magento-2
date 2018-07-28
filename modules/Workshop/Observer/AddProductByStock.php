<?php

namespace PleaseWork\Workshop\Observer;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use PleaseWork\Workshop\Block\GetStockConfig;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Data\Form\FormKey;
use PleaseWork\Workshop\Helper\ObsStock;
use Magento\Checkout\Model\Session;

/**
 * Class AddProductByStock
 * @package PleaseWork\Workshop\Observer
 */
class AddProductByStock implements ObserverInterface
{
    /**
     * @var array
     */
    protected $dataAllItems = [];

    /**
     * AddProductByStock constructor.
     * @param GetStockConfig $stockConfig
     * @param ProductRepository $productRepository
     * @param FormKey $formKey
     * @param ProductFactory $productFactory
     * @param ObsStock $obsStock
     * @param Session $session
     */
    public function __construct(
        GetStockConfig $stockConfig,
        ProductRepository $productRepository,
        FormKey $formKey,
        ProductFactory $productFactory,
        ObsStock $obsStock,
        Session $session
    ) {
        $obsStock::_construct(
            $stockConfig,
            $productRepository,
            $formKey,
            $productFactory,
            $session
        );
    }

    /**
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $cart = $observer->getEvent()->getCart();
        $quote = $cart->getQuote();
        $quoteAllItems = $quote->getAllItems();

        ObsStock::setValues();
        $deleted = ObsStock::isDeletedGift($quote);
        $giftInCart = ObsStock::checkGift($quoteAllItems);


        if (ObsStock::checkBundle($quoteAllItems)) {
            ObsStock::appendSplit($quoteAllItems, $cart, $quote);
        }

        if (ObsStock::clearPrice($quoteAllItems) > ObsStock::$customMaxPrice and $deleted !== true) {
            ObsStock::add(ObsStock::$giftId, $cart, ObsStock::$qty);
            ObsStock::instancePrice(
                null,
                ObsStock::$freePrice,
                ObsStock::$qty,
                $quoteAllItems
            );
        }

        if ($giftInCart and ObsStock::clearPrice($quoteAllItems) < ObsStock::$customMaxPrice) {
            ObsStock::instancePrice(
                null,
                ObsStock::$oldPrice,
                ObsStock::$qtyCart,
                $quoteAllItems
            );
        }
    }
}
