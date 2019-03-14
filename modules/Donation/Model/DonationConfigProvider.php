<?php

namespace Sandftae\Donation\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\ObjectManager;

/**
 * Class DonationConfigProvider
 * @package Sandftae\Donation\Model
 */
class DonationConfigProvider implements ConfigProviderInterface
{
    /**
     * Path to config enable checkout module.
     */
    const STATUS = 'sandftae_checkout_fields/general/enable_sandftae_checkout';

    /**
     * Path to config short text for checkout module.
     */
    const TITLE = 'sandftae_checkout_fields/general/sandftae_short_text_checkout';

    /**
     * Path to config rates for checkout module.
     */
    const RATES = 'sandftae_checkout_fields/general/sandftae_rates_checkout';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * CheckoutConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Json|null $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Json $serializer = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Get values for checkout module.
     *
     * @return array
     */
    public function getConfig()
    {
        $donationConfig['checkoutDonation'] = [
            'donationEnable' => $this->getModuleEnable(),
            'donationShortDescription' => $this->getTitle(),
            'donationRates' => $this->getRates(),
        ];
        return $donationConfig;
    }


    /**
     * Get enable value for checkout module.
     *
     * @return boolean
     */
    public function getModuleEnable()
    {
        $result = boolval($this->scopeConfig->getValue(
            self::STATUS,
            ScopeInterface::SCOPE_STORE
        ));
        return $result;
    }

    /**
     * Get checkout short description.
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->scopeConfig->getValue(
            self::TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get checkout rates.
     *
     * @return mixed
     */
    public function getRates()
    {
        $stringRates = $this->scopeConfig->getValue(
            self::RATES,
            ScopeInterface::SCOPE_STORE
        );

        $rates = [];
        $values = $this->serializer->unserialize($stringRates);
        foreach ($values as $key => $value) {
            if (is_array($value) && array_key_exists('rate_price', $value)) {
                $rates[] = $value['rate_price'];
            }
        }
        return $rates;
    }
}
