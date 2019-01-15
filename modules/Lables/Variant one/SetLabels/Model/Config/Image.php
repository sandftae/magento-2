<?php

namespace PleaseWork\SetLabels\Model\Config;

use \Magento\Config\Model\Config\Backend\Image as MagentoImage;

/**
 * Class Image
 * @package PleaseWork\SetLabels\Model\Config
 */
class Image extends MagentoImage
{
    /**
     * The tail part of directory path for uploading
     *
     */
    const UPLOAD_DIR = 'catalog/product/watermark/default';

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir():string
    {
        return  $this->_mediaDirectory->getAbsolutePath(self::UPLOAD_DIR);
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo():bool
    {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return string[]
     */
    protected function _getAllowedExtensions():array
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
