<?php

namespace Sandftae\CustomShipping\Model;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Sandftae\CustomShipping\Api\ShippingFrontValidateInterface;
use Sandftae\CustomShipping\Api\ShippingRepositoryInterface;

/**
 * Class ShippingFrontValidate
 *
 * @package Sandftae\CustomShipping\Model
 */
class ShippingFrontValidate extends AbstractCarrier implements CarrierInterface, ShippingFrontValidateInterface
{
    const FIXED_COUNTRY = 'USA';

    /**
     * @var string
     */
    protected $_code = 'simpleshipping';

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var ShippingRepositoryInterface
     */
    protected $shippingRepository;

    /**
     * ShippingFrontValidate constructor.
     *
     * @param ScopeConfigInterface          $scopeConfig
     * @param ErrorFactory                  $rateErrorFactory
     * @param LoggerInterface               $logger
     * @param ResultFactory                 $rateResultFactory
     * @param MethodFactory                 $rateMethodFactory
     * @param ShippingRepositoryInterface   $shippingRepository
     * @param array                         $data
     */
    public function __construct(
        ScopeConfigInterface        $scopeConfig,
        ErrorFactory                $rateErrorFactory,
        LoggerInterface             $logger,
        ResultFactory               $rateResultFactory,
        MethodFactory               $rateMethodFactory,
        ShippingRepositoryInterface $shippingRepository,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->shippingRepository = $shippingRepository;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }


    /**
     * get allowed methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param   null    $country
     * @return float
     */
    public function getShippingPrice($country = null)
    {
        $configPrice = 0;
        if (!$country) {
            $configPrice += $this->getConfigData('price');
        } else {
            $configPrice +=$country->getPrice();
        }
        $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);

        return $shippingPrice;
    }

    /**
     * @param   RateRequest     $request
     * @return  bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $selectedCountryAbbr = $request->getDestCountryId();
        $country = $this->shippingRepository->getCountry($selectedCountryAbbr);

        if ($country) {
            $method->setCarrierTitle('Shipping cost for ' . $country->getCountryName());
            $amount = $this->getShippingPrice($country);
        } else {
            $amount = $this->getShippingPrice();
            $method->setCarrierTitle($this->getConfigData('title'));
        }

        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }
}
