<?php

namespace PleaseWork\Workshop\Helper;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use PleaseWork\Workshop\Block\GetStockConfig;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Session;

/**
 * Class ObsStock
 * @package PleaseWork\Workshop\Helper
 */
class ObsStock
{
    /**
     * @var stockConfig
     */
    public static $stockConfig;

    /**
     * @var productRepository
     */
    public static $productRepository;

    /**
     * @var productModel
     */
    public static $productModel;

    /**
     * @var formKey
     */
    public static $formKey;

    /**
     * @var $customMaxPrice
     */
    public static $customMaxPrice;

    /**
     * @var $giftId
     */
    public static $giftId;

    /**
     * @var $oldPrice
     */
    public static $oldPrice;

    /**
     * @var $freePrice
     */
    public static $freePrice;

    /**
     * @var $giftSku
     */
    public static $giftSku;
    /**
     * @var $qty
     */
    public static $qty;

    /**
     * @var int
     */
    public static $correctTotal = 0;

    /**
     * @var $session
     */
    public static $session;
    /**
     * @var array
     */
    public static $skuArray = [];

    /**
     * @var $qtyCart
     */
    public static $qtyCart;

    /**
     * @param GetStockConfig $stockConfig
     * @param ProductRepository $productRepository
     * @param FormKey $formKey
     * @param ProductFactory $productFactory
     * @param Session $session
     */
    public static function _construct(
        GetStockConfig $stockConfig,
        ProductRepository $productRepository,
        FormKey $formKey,
        ProductFactory $productFactory,
        Session $session = null
    ) {
        self::$stockConfig          = $stockConfig;
        self::$productRepository    = $productRepository;
        self::$productModel         = $productFactory->create();
        self::$formKey              = $formKey;
        self::$session              = $session;
    }

    /**
     * @param int $id
     * @param $cart
     * @param int $qty
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public static function add($id, &$cart, int $qty)
    {
        $gift = self::$productRepository->getById($id);
        $params = array(
            'form_key' => self::$formKey->getFormKey(),
            'product' => $gift->getId(),
            'qty' => $qty
        );
        $cart->addProduct($gift, $params);
    }

    /**
     * @param $quoteAllItems
     * @return int
     */
    public static function clearPrice($quoteAllItems)
    {
        $correctTotal = 0;
        foreach ($quoteAllItems as $item) {
            if ($item->getSku() == self::$giftSku) {
                continue;
            }

            if (count($item->getChildren()) > 0) {
                continue;
            }

            if (in_array($item->getSku(), self::getStocksProduct())) {
                continue;
            }
            $correctTotal = self::recalculate($item->getQty(), $item->getPrice(), $item);
        }
        return $correctTotal;
    }

    /**
     * @param $quoteAllItems
     * @param $cart
     * @param $quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public static function appendSplit(&$quoteAllItems, $cart, $quote)
    {
        foreach ($quoteAllItems as $item) {
            $children = $item->getChildren();
            $key = key($quoteAllItems);
            if (count($children) > 0) {
                foreach ($children as $child) {
                    $idProductForAddToCart = ObsStock::getIdBySku($child->getSku());

                    self::add($idProductForAddToCart, $cart, 1);

                    self::$skuArray[] = $child->getSku();

                    unset($quoteAllItems[$key]);
                }
                self::saveSkuInSess(self::$skuArray);

                $quote->deleteItem($item);
            }
        }

        foreach ($quote->getAllItems() as $item) {
            if (in_array($item->getSku(), self::$skuArray)) {
                ObsStock::instancePrice($item, 60);
            }
        }
    }

    /**
     * @param $quoteAllItems
     * @return bool
     */
    public static function checkGift($quoteAllItems)
    {
        $skuInfo = [];

        foreach ($quoteAllItems as $item) {
            if ($item->getSku() === self::$giftSku) {
                $skuInfo = ['qty' => $item->getQty(), 'sku' => $item->getSku()];
                self::$qtyCart = $item->getQty();
            }
        }

        self::saveSkuInSess($skuInfo);

        return !empty($skuInfo) ? true : false;
    }

    /**
     * @param $quoteAllItems
     * @return bool
     */
    public static function checkBundle(&$quoteAllItems)
    {
        foreach ($quoteAllItems as $item) {
            if (count($item->getChildren()) > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $qty
     * @param $price
     * @param null $item
     * @return int
     */
    public static function recalculate($qty, $price, $item = null):int
    {
        $correctTotal = 0;
        if ($price == null) {
            $price = self::getPriceBySku($item->getSku());
        }
        for ($i = 0; $i < $qty; $i++) {
            $correctTotal += (int) $price;
        }
        return (int) $correctTotal;
    }

    /**
     * @param null $item
     * @param int $price
     * @param int|null $qty
     * @param array|null $dataAllItems
     * @return bool
     */
    public static function instancePrice($item = null, int $price, int $qty = null, array &$dataAllItems = null)
    {
        if ($qty !== null and $item !== null) {
            self::instanceQty($item, $qty);
        }
        if ($dataAllItems !== null) {
            foreach ($dataAllItems as $item) {
                if ($item->getSku() == self::$giftSku) {
                    self::instanceQty($item, $qty);
                    self::preventivePrice($item, $price);
                    return true;
                }

                $middleWareQuote = $item->getQuote() ?? null;
                $middleWareItems = $middleWareQuote->getAllItems() ?? null;

                if ($middleWareItems !== null) {
                    foreach ($middleWareItems as $middleWareItem) {
                        if ($middleWareItem->getSku() == self::$giftSku) {
                            self::instanceQty($middleWareItem, $qty);
                            self::preventivePrice($middleWareItem, $price);
                            return true;
                        }
                    }
                }
            }
            return true;
        }
        self::preventivePrice($item, $price);
    }

    /**
     * @param $item
     * @param int $price
     */
    protected static function preventivePrice($item, int $price)
    {
        $item->setCustomPrice($price);
        $item->setOriginalCustomPrice($price);
        $item->getProduct()->setIsSupermodel(true);
    }

    /**
     * @param $item
     * @param int $qty
     */
    protected static function instanceQty($item, int $qty)
    {
        $item->setQty($qty);
        $item->setOriginalCustomQty($qty);
        $item->getProduct()->setIsSupermodel(true);
    }

    /**
     * @param $quote
     * @return mixed
     */
    public static function isDeletedGift($quote)
    {
        $items = $quote->getItems();

        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item->getSku() == self::$giftSku) {
                    return $item->isDeleted();
                }
            }
        }
        return null;
    }

    /**
     * @param string $sku
     * @return int
     */
    public static function getIdBySku($sku):int
    {
        return  (int) self::$productModel->loadByAttribute('sku', $sku)->getId();
    }

    /**
     * @param string $sku
     * @return int
     */
    public static function getPriceBySku(string $sku):int
    {
        return (int) self::$productModel->loadByAttribute('sku', $sku)->getData('price');
    }

    /**
     * @param string $sku
     * @return int
     */
    public static function getPriceBySkuFromCart(string $sku):int
    {
        return (int) self::$productModel->loadByAttribute('sku', $sku)->getData('price');
    }

    /**
     * @return void
     */
    public static function setValues()
    {
        self::$giftSku = self::$stockConfig->getScopeShow()->getValue(GetStockConfig::PRODUCT_SKU_CONFIG);

        self::$customMaxPrice = (int) self::$stockConfig->getScopeShow()
                                                    ->getValue(GetStockConfig::PRODUCT_TOTAL_PRICE_CONFIG);

        self::$giftId = (int) self::getIdBySku(self::$giftSku);

        self::$oldPrice = (int) self::getPriceBySku(self::$giftSku);

        self::$freePrice = (int) self::$stockConfig->getScopeShow()
                                                ->getValue(GetStockConfig::PRODUCT_PRICE_CONFIG);
        self::$qty = (int) self::$stockConfig->getScopeShow()
                                            ->getValue(GetStockConfig::PRODUCT_QUANTITY_CART);
    }

    /**
     * @param array $bundle
     *
     * @return void
     */
    public static function saveSkuInSess(array $bundle)
    {
        if (!isset(self::$session->getData('steps')['stocks_test'])) {
            self::$session->setStepData('stocks_test', $bundle);
        } else {
            if (count(self::$session->getData('steps')['stocks_test']) > 0) {
                $sessionValues = self::$session->getData('steps')['stocks_test'];
                self::$session->setStepData('stocks_test', array_unique(array_merge($sessionValues, $bundle)));
            }
        }
    }

    /**
     * @return mixed
     */
    public static function getStocksProduct()
    {
        return self::$session->getData('steps')['stocks_test'];
    }
}
