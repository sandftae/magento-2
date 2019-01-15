# Prerequisites
- Magento 1 supposed to be untouched during the migration. 
  - Run this command from Magento 2 installation directory to completely reinstall Magento 2
+ Migration tool supposed to be executed many times, so you will need a script to clear Magento 2 database many times, e.g.
bash ../samples/install.sh
  + Migration tool supposed to be executed many times, so don't do anything important from admin panel. As this will be lost
Migration tool vocabulary: Document in migration tool is a table in database, field in migration tool is a column in database
Working with migration
define your Magento 1 and Magento 2 versions. We are using Magento CE 1.9.3.10 and Magento CE 2.2.6 in this project
install migration tool within Magento 2 application
composer config repositories.magento composer https://repo.magento.com
composer require magento/data-migration-tool:<Magento 2 version>
copy configuration file from 
```<your magento 2 installation dir>/vendor/magento/data-migration-tool/etc/<migration edition>/<ce or version> to some git tracked directory, e.g. <your Magento 2 installation directory>/migration, e.g.
cp ./vendor/magento/data-migration-tool/etc/<migration edition>/<ce or version>/config.xml.dist ./migration/config.xml
within copied config.xml specify credentials for accessing databases:
<source>
    <database host="127.0.0.1" name="magento1" user="root" password="" port=""/>
</source>
<destination>
    <database host="127.0.0.1" name="magento2" user="root"/>
</destination>
<options>
    <source_prefix>mage1_</source_prefix><!-- optional -->
    <dest_prefix>mage2_</dest_prefix><!-- optional -->
    <crypt_key>d27a50f04c0b5879b48478a756ab2273</crypt_key><!-- mandatory -->
</options>
```
migrate settings
php bin/magento migrate:settings local.xml
migrate data
php bin/magento migrate:data local.xml
Migration process customization
transforming data during migration You just need to add <transform> node to your map.xml
<destination>
    <field_rules>
        <transform>
            <field>some_value.some_field</field>
            <handler class="\Migration\Handler\SetValue">
                <param name="value" value="Some Custom Title"/>
            </handler>
        </transform>
    </field_rules>
</destination>
You can define multiple handlers for one column. You can use predefined handlers (usually they belong to \Migration\Handler namespace), or specify your own handlers.

exclude document from migration process. To do this you need to add ignore node to your map.xml
<source>
    <document_rules>
        <ignore>
            <document>paybox_question_number</document>
        </ignore>
    </document_rules>
</source>
migrate custom table. There is no need to customize anything. By default migration tool migrates everything, that exists in both Magento 1 and Magento 2 db versions and has the same amount of columns. So if some custom table exists in both Magento 1 and Magento 2 databases, you do not need to configure anything
the table you want to migrate has been renamed in Magento 2. In this case you would probably get error like
[2018-10-21 05:40:55][ERROR]: Source documents are not mapped: migration_table2 [2018-10-21 05:40:55][ERROR]: Destination documents are not mapped: migration_table2_renamed

In order to figure out this you need to add <rename> node to source->document_rules section. e.g.

<source>
    <document_rules>
        <rename>
            <document>migration_table2</document>
            <to>migration_table2_renamed</to>
        </rename>
    </document_rules>
</source>
the table you want to migrate has columns which have been renamed in Magento 2. In this case you would probably get error like
[2018-10-21 06:01:06][ERROR]: Source fields are not mapped. Document: migration_table3. Fields: title [2018-10-21 06:01:06][ERROR]: Destination fields are not mapped. Document: migration_table3. Fields: title_renamed

In order to figure out this you need to add <move> node to source->field_rules section. e.g.

<source>
    <field_rules>
        <move>
            <field>migration_table3.title</field>
            <to>migration_table3.title_renamed</to>
        </move>
    </field_rules>
</source>
destination database has some extra columns which does not exist in Magento 1. Migration Tool will notify you about that. [2018-10-21 06:38:38][ERROR]: Destination fields are not mapped. Document: migration_table4. Fields: title_new_optional,title_new_required Ignoring such column is as easy as adding next node to your map.xml
<destination>
    <field_rules>
        <ignore>
            <field>migration_table4.title_new_optional</field>
        </ignore>
    </field_rules>
</destination>
Another option would be to transform some data for this column. The xml declaration would be a bit tricky:

<destination>
    <field_rules>
        <ignore>
            <field>migration_table4.title_new_required</field>
        </ignore>
        <transform>
            <field>migration_table4.title_new_required</field>
            <handler class="\Migration\Handler\SetValue">
                <param name="value" value="Some Custom Title"/>
            </handler>
        </transform>
    </field_rules>
</destination>
the same way we can ignore field in document
<source>
    <field_rules>
        <ignore>
            <field>customer_eav_attribute.is_used_for_customer_segment</field>
        </ignore>
    </document_rules>
</source>
Tricks
Convert error message about not mapped fields into <ignore> nodes in PhpStorm. Press Ctrl + R, select "Regex" mode, and replace (\w*), with \<ignore\>\n\t\<field\>sales_flat_creditmemo.$1\</field\>\n\</ignore>\n