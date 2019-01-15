<?php

namespace PleaseWork\Labels\Model\Labels\Settings;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class LabelsSettings
 * @package PleaseWork\SetLabels\Block
 * @package PleaseWork\SetLabels\Block
 */
class LabelsSettings extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    public $scopeConfig;

    /**
     * GetStockConfig constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return ScopeConfigInterface
     */
    public function getScopeShow()
    {
        return $this->scopeConfig;
    }

    /**
     * @return array
     */
    protected function getArrayPath():array
    {
        return [
            'workshop_labels/*/image_size',
            'workshop_labels/*/label_image',
            'workshop_labels/*/label_position',
            'workshop_labels/*/label_opacity'

        ];
    }

    /**
     * @return array
     */
    protected function getNameToPath():array
    {
        return ['size', 'label', 'position', 'opacity'];
    }

    /**
     * @return array
     */
    protected function getType():array
    {
        return ['new', 'bestseller', 'discount'];
    }

    /**
     *  Dynamically create variables in this class
     *
     * @return void
     */
    public function dynamo()
    {
        foreach ($this->getType() as $keyFirst => $firstPartVar) {
            $stackPath = array_unique($this->createPath($firstPartVar));
            foreach ($this->getNameToPath() as $keySecond => $secondPart) {
                $this->{$firstPartVar.ucfirst($secondPart)} = $stackPath[$keySecond];
            }
        }
    }

    /**
     * Create an array of paths
     *
     * @param string $partPath
     * @return array
     */
    public function createPath(string $partPath):array
    {
        $fullPath = [];
        foreach ($this->getArrayPath() as $key => $path) {
            $fullPath[] = $this->supportCreatePath($partPath, $path);
        }
        return $fullPath;
    }

    /**
     * Return one correct path
     *
     * @param string $partPath
     * @param string $path
     * @return string
     */
    protected function supportCreatePath(string $partPath, string $path):string
    {
        return str_replace('*', 'label_' . $partPath, $path);
    }
}
