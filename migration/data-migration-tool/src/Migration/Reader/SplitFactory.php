<?php

namespace Migration\Reader;

/**
 * Class SplitFactory
 * @package Migration\Reader
 */
class SplitFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface|null
     */
    protected $objectManager = null;

    /**
     * @var string|null
     */
    protected $instanceName = null;

    /**
     * @var \Migration\Config
     */
    protected $config;

    /**
     * SplitFactory constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Migration\Config $config
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Migration\Config $config,
        $instanceName = \Migration\Reader\Split::class
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->instanceName = $instanceName;
    }

    /**
     * @param $configOption
     * @return mixed
     */
    public function create($configOption)
    {
        $mapFile = $this->config->getOption($configOption);
        return $this->objectManager->create($this->instanceName, ['mapFile' => $mapFile]);
    }
}