<?php

namespace Sandftae\Labels\Helper\Config;

use Sandftae\Labels\Model\Labels\Settings\LabelsSettings;

/**
 * Class InstanceValues
 * @package Sandftae\Labels\Helper\Config
 */
class InstanceValues
{
    /**
     *   Block for products with the type "new"
     */
    public $newSize;
    public $newLabel;
    public $newPosition;
    public $newOpacity;

    /**
     *   Block for products with the type "bestseller"
     */
    public $bestsellerSize;
    public $bestsellerLabel;
    public $bestsellerPosition;
    public $bestsellerOpacity;

    /**
     *   Block for products with the type "discount"
     */
    public $discountSize;
    public $discountLabel;
    public $discountPosition;
    public $discountOpacity;

    /**
     * @var LabelsSettings
     */
    public $settings;

    /**
     * @var \ReflectionClass
     */
    public $settingsReflection;


    /**
     * InstanceValues constructor.
     * @param LabelsSettings $labelsSettings
     */
    public function __construct(LabelsSettings $labelsSettings)
    {
        $labelsSettings->dynamo();
        $this->settings = $labelsSettings;
        $this->settingsReflection = new \ReflectionObject($labelsSettings);
    }

    /**
     * @return array
     */
    protected function getPropertiesSettings():array
    {
        return $this->settingsReflection->getProperties();
    }

    /**
     * @return void
     */
    public function instance()
    {
        foreach ($this->getPropertiesSettings() as $properties) {
            if (property_exists($this, $properties->getName())) {
                $this->{$properties->getName()} = $this->settings->
                                                  getScopeShow()->getValue($this->settings->{$properties->getName()});
            }
        }
    }
}
