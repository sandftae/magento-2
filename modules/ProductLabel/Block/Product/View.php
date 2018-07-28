<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category  Magecom
 * @package   Magecom_Label
 * @copyright Copyright (c) 2017 Magecom, Inc. (http://www.magecom.net)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

namespace Magecom\ProductLabel\Block\Product;

use Magento\Framework\View\Element\Template;

/**
 * Class View
 */
class View extends Template
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $_productResource;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * View constructor.
     * @param Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_productResource = $productResource;
        parent::__construct($context, $data);
    }

    /**
     * @param $name
     * @return array|bool|string
     */
    public function getAttributeValue($name)
    {
        return $this->_productResource->getAttributeRawValue(
            $this->getCurrentProduct()->getId(),
            $name,
            $this->_storeManager->getStore()->getId());
    }

    /**
     * @return null|\Magento\Catalog\Model\Product
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

}
