<?php

namespace PleaseWork\SetLabels\Helper\Label;

/**
 * Class Constants
 * @package PleaseWork\SetLabels\Helper\Label
 */
class Constants
{

    public static $test1;
    public static $test12;
    public static $test13;


    /**
     *  Block to total settings constants
     */
    const MODULE_ENABLE   = 'workshop_system_labels/general/enable';
    const PROMOTION_START = 'workshop_system_labels/general/data_start_promotion';
    const PROMOTION_END   = 'workshop_system_labels/general/data_end_promotion';

    /**
     * Block for constants bundle-type products
     */
    const BUNDLE_TYPE           = 'workshop_system_labels/label_bundle_product/label_selected_type';
    const BUNDLE_SIZE           = 'workshop_system_labels/label_bundle_product/image_size';
    const BUNDLE_DESCRIPTION    = 'workshop_system_labels/label_bundle_product/label_description_selected_product';
    const BUNDLE_IMAGE          = 'workshop_system_labels/label_bundle_product/label_image';
    const BUNDLE_POSITION       = 'workshop_system_labels/label_bundle_product/label_position';
    const BUNDLE_OPACITY        = 'workshop_system_labels/label_bundle_product/label_opacity';
    const BUNDLE_NEW      = 'workshop_system_labels/label_bundle_product/new_product_with';

    /**
     * Block for constants simple-type products
     */
    const SIMPLE_TYPE           = 'workshop_system_labels/label_simple_product/label_selected_type';
    const SIMPLE_SIZE           = 'workshop_system_labels/label_simple_product/image_size';
    const SIMPLE_DESCRIPTION    = 'workshop_system_labels/label_simple_product/label_description_selected_product';
    const SIMPLE_IMAGE          = 'workshop_system_labels/label_simple_product/label_image';
    const SIMPLE_POSITION       = 'workshop_system_labels/label_simple_product/label_position';
    const SIMPLE_OPACITY        = 'workshop_system_labels/label_simple_product/label_opacity';
    const SIMPLE_NEW       = 'workshop_system_labels/label_simple_product/new_product_with';

    /**
     * Block for constants configurable-type products
     */
    const CONFIGURABLE_TYPE         = 'workshop_system_labels/label_configurable_product/label_selected_type';
    const CONFIGURABLE_SIZE         = 'workshop_system_labels/label_configurable_product/image_size';
    const CONFIGURABLE_DESCRIPTION  = 'workshop_system_labels/label_configurable_product/label_description_selected_product';
    const CONFIGURABLE_IMAGE        = 'workshop_system_labels/label_configurable_product/label_image';
    const CONFIGURABLE_POSITION     = 'workshop_system_labels/label_configurable_product/label_position';
    const CONFIGURABLE_OPACITY      = 'workshop_system_labels/label_configurable_product/label_opacity';
    const CONFIGURABLE_NEW     = 'workshop_system_labels/label_configurable_product/new_product_with';

    /**
     * Block for constants all products
     */
    const ALL_TYPE          = 'workshop_system_labels/label_all_type_product/label_selected_type';
    const ALL_SIZE          = 'workshop_system_labels/label_all_type_product/image_size';
    const ALL_DESCRIPTION   = 'workshop_system_labels/label_all_type_product/label_description_selected_product';
    const ALL_IMAGE         = 'workshop_system_labels/label_all_type_product/label_image';
    const ALL_POSITION      = 'workshop_system_labels/label_all_type_product/label_position';
    const ALL_OPACITY       = 'workshop_system_labels/label_all_type_product/label_opacity';
    const ALL_NEW      = 'workshop_system_labels/label_all_type_product/new_product_with';

    /**
     * Block for constants unique-type products
     */
    const UNIQUE_TYPE       = 'workshop_system_labels/unique_product/label_selected_type';
    const UNIQUE_SIZE          = 'workshop_system_labels/unique_product/image_size';
    const UNIQUE_DESCRIPTION   = 'workshop_system_labels/unique_product/label_description_selected_product';
    const UNIQUE_IMAGE         = 'workshop_system_labels/unique_product/label_image';
    const UNIQUE_POSITION      = 'workshop_system_labels/unique_product/label_position';
    const UNIQUE_OPACITY       = 'workshop_system_labels/unique_product/label_opacity';
    const UNIQUE_NEW      = 'workshop_system_labels/unique_product/new_product_with';
    const UNIQUE_SKU           = 'workshop_system_labels/unique_product/unique_sku';
}
