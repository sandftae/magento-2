<?php
/**
 * Magecom_SetLabels LabelsSettings::class
 *
 * @category Magecom
 * @package Magecom_Workshop
 * @author Magecom
 */
namespace PleaseWork\SetLabels\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class LabelsSettings
 * @package PleaseWork\SetLabels\Block
 * @package PleaseWork\SetLabels\Block
 */
class LabelsSettings extends Template
{
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
