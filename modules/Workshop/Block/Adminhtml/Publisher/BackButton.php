<?php

namespace PleaseWork\Workshop\Block\Adminhtml\Publisher;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class BackButton
 * @package PleaseWork\Workshop\Block\Adminhtml\Post
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/publisher/display');
    }
}
