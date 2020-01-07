<?php

/**
 * @category GraphQL_Blog
 * @copyright Copyright (c) 2019 GraphQL_Blog
 */

declare(strict_types=1);

namespace GraphQL\Blog\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use GraphQL\Blog\Model\CustomerReviewFactory;

/**
 * Class AddTestFieldsToCustomerReviewTable
 *
 * @package GraphQL\Blog\Setup\Patch\Data
 */
class AddTestFieldsToCustomerReviewTable implements DataPatchInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleSetup;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var CustomerReviewFactory
     */
    private $customerReviewFactory;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup,
        Config $eavConfig,
        CustomerReviewFactory $customerReviewFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerReviewFactory = $customerReviewFactory;
        $this->moduleSetup = $moduleDataSetup;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        try {
            for ($i = 0; $i <= 5; $i++) {
                $review = $this->customerReviewFactory->create();
                $review->setProductId($i);
                $review->setCustomerId($i);
                $review->setCustomerMessage('test message ' . $i);
                $review->save();
            }
        } catch (\Exception $exception) {
            /** TODO: do nothing */
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
