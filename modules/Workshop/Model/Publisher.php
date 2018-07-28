<?php

namespace PleaseWork\Workshop\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Publisher
 * @package PleaseWork\Workshop\Model
 */
class Publisher extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'pleasework_workshop_publisher';

    # Reserves name a the variable
    protected $_cacheTag = 'pleasework_workshop_publisher';

    # Reserves name a the variable
    protected $_eventPrefix = 'pleasework_workshop_publisher';

    /**
     * Define model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('PleaseWork\Workshop\Model\ResourceModel\Publisher');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return [];
    }
}
