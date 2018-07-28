<?php
/**
 * Magecom_SetLabels Calendar::class
 *
 * @category Magecom
 * @package Magecom_Workshop
 * @author Magecom
 */
namespace PleaseWork\SetLabels\Block\Date;

use Magento\Framework\Registry;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Calendar
 * @package PleaseWork\SetLabels\Block\Date
 */
class Calendar extends Field
{
    /**
     * @var  Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $baseURL = $this->getBaseUrl();
        $html = $element->getElementHtml();
        $calpath = $baseURL . 'pub/media/systemcalendar/';
        if (!$this->coreRegistry->registry('datepicker_loaded')) {
            $html .= '<style type="text/css">input.datepicker { background-image: url(' . $calpath . 'calendar.png) !important; background-position: calc(100% - 8px) center; background-repeat: no-repeat; } input.datepicker.disabled,input.datepicker[disabled] { pointer-events: none; }</style>';
            $this->coreRegistry->registry('datepicker_loaded', 1);
        }
        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/ui"], function () {
                jQuery(document).ready(function () {
                    jQuery("#' . $element->getHtmlId() . '").datepicker( { dateFormat: "dd/mm/yy" } );
                        
                    var el = document.getElementById("' . $element->getHtmlId() . '");
                    el.className = el.className + " datepicker";
                });
            });
            </script>';
        return $html;
    }
}
