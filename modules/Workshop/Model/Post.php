<?php

namespace PleaseWork\Workshop\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Post
 * @package PleaseWork\Workshop\Model
 */
class Post extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'pleasework_workshop_post';

    # Reserves name a the variable
    protected $_cacheTag = 'pleasework_workshop_post';

    # Reserves name a the variable
    protected $_eventPrefix = 'pleasework_workshop_post';

    /**
     * Define model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('PleaseWork\Workshop\Model\ResourceModel\Post');
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
        $values = [];

        return $values;
    }
}
