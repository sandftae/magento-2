<?php

namespace PleaseWork\Workshop\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('pleasework_workshop_post')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('pleasework_workshop_post')
            )
                ->addColumn(
                    'post_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Post ID'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'Post Name'
                )
                ->addColumn(
                    'url_key',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Post URL Key'
                )
                ->addColumn(
                    'post_content',
                    Table::TYPE_TEXT,
                    '64k',
                    [],
                    'Post Post Content'
                )
                ->addColumn(
                    'tags',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Post Tags'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_INTEGER,
                    1,
                    [],
                    'Post Status'
                )
                ->addColumn(
                    'featured_image',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Post Featured Image'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                    'Updated At')
                ->setComment('Post Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('pleasework_workshop_post'),
                $setup->getIdxName(
                    $installer->getTable('pleasework_workshop_post'),
                    ['name','url_key','post_content','tags','featured_image'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name','url_key','post_content','tags','featured_image'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }

        if (!$installer->tableExists('pleasework_workshop_messages')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('pleasework_workshop_messages')
            )
                ->addColumn(
                    'message_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ],
                    'Message ID'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable => false'],
                    'User Name'
                )
                ->addColumn(
                    'email',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'User email'
                )
                ->addColumn(
                    'message',
                    Table::TYPE_TEXT,
                    '350k',
                    [],
                    'User message'
                )
                ->addColumn(
                    'phone',
                    Table::TYPE_INTEGER,
                    11,
                    [],
                    'Phone number'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->setComment('User messages Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('pleasework_workshop_messages'),
                $setup->getIdxName(
                    $installer->getTable('pleasework_workshop_messages'),
                    ['name','message'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name','message'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $installer->endSetup();
    }
}
