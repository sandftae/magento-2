<?php

namespace Sandftae\Donation\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Sandftae\Donation\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Add new column to sales_order, quote and sales_invoice tables.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Update table 'sales_order'
         */
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order'),
            'donation',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => true,
                'comment' => 'Donation'
            ]
        );

        /**
         * Update table 'sales_invoice'
         */
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_invoice'),
            'donation',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => true,
                'comment' => 'Donation'
            ]
        );

        /**
         * Update table 'quote'
         */
        $setup->getConnection()->addColumn(
            $setup->getTable('quote'),
            'donation',
            [
                'type' => Table::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => true,
                'comment' => 'Donation'
            ]
        );

        $installer->endSetup();
    }
}
