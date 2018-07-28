<?php

namespace PleaseWork\Workshop\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class FeedBack
 * @package PleaseWork\Workshop\Model
 */
class FeedBack extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'pleasework_workshop_messages';

    protected $_cacheTag = 'pleasework_workshop_messages';

    protected $_eventPrefix = 'pleasework_workshop_messages';

    protected function _construct()
    {
        $this->_init('PleaseWork\Workshop\Model\ResourceModel\FeedBack');
    }

    /**
     * @return null|string[]
     */
    public function getIdentities()
    {
        return null;
    }
}