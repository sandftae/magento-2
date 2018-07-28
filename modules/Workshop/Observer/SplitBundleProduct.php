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

/**
 * Class SplitBundleProduct
 * @package PleaseWork\Workshop\Observer
 */
class SplitBundleProduct implements ObserverInterface
{
    /**
     * @var GetStockConfig
     */
    protected $show;

    /**
     * @var Productrepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productModel;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var observer Helper
     */
    protected $obsHlp;

    /**
     * @var array
     */
    protected $skuArray = [];

    /**
     * SplitBundleProduct constructor.
     * @param GetStockConfig $stockConfig
     * @param ProductRepository $productRepository
     * @param FormKey $formKey
     * @param ProductFactory $productFactory
     * @param ObsStock $ObsStock
     */
    public function __construct(
        GetStockConfig $stockConfig,
        ProductRepository $productRepository,
        FormKey $formKey,
        ProductFactory $productFactory,
        ObsStock $ObsStock
    ) {
        $ObsStock::_construct(
            $stockConfig,
            $productRepository,
            $formKey,
            $productFactory
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

        foreach ($quoteAllItems as $item) {
            $children = $item->getChildren();
            if (count($children) > 0) {
                foreach ($children as $child) {
                    $idProductForAddToCart = ObsStock::getIdBySku($child->getSku());

                    ObsStock::add($idProductForAddToCart, $cart, 1);

                    $this->skuArray[] = $child->getSku();

                    $quote->deleteItem($item);
                }
            }
        }

        foreach ($quote->getAllItems() as $item) {
            if (in_array($item->getSku(), $this->skuArray)) {
                ObsStock::instancePrice($item, 60);
            }
        }
    }
}
