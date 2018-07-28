<?php

namespace PleaseWork\Workshop\Block\Adminhtml\Publisher;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 * @package PleaseWork\Workshop\Block\Adminhtml\Post
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getPublisherId()) {
            $data = [
                'label' => __('Delete publisher'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['publisher_id' => $this->getPublisherId()]);
    }
}
