<?php

namespace PleaseWork\Workshop\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class GetStockConfig
 * @package PleaseWork\Workshop\Block
 */
class GetStockConfig extends Template
{

    const PRODUCT_PRICE_CONFIG          = 'workshop_system_observer/general/price';

    const PRODUCT_TOTAL_PRICE_CONFIG    = 'workshop_system_observer/general/total_price';

    const PRODUCT_SKU_CONFIG            = 'workshop_system_observer/general/product_sku';

    const PRODUCT_STATUS_CONFIG         = 'workshop_system_observer/general/enable';

    const PRODUCT_DESCRIPTION_CONFIG    = 'workshop_system_observer/general/display_text';

    const PRODUCT_QUANTITY_CART         = 'workshop_system_observer/general/quantity';

    const PRODUCT_STOCK_ACTIVE          = 'workshop_system_observer/general/enable';

    const PRODUCT_REPORT_MAIL           = 'workshop_system_observer/general/report_mail';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * GetStockConfig constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return ScopeConfigInterface
     */
    public function getScopeShow()
    {
        return $this->scopeConfig;
    }
}
