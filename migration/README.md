
#### prolog
При работе с миграцией возникла необходимость разбить некоторые таблицы. Т.к. в `data-migration-tool` сплит таблицы
возможен только через свой `custom step` (что логично и, думаю, единственно верно), идея расширить функционал (с целью повысить понимание инструмента) показалась интересной. В примере есть dump БД с sample data. Также есть SQL-procedure для заполнения данными таблиц, которые создавались для  `split_process`. 
Таблицы имеют приставку `migration`.

В директории `transferred_data` dump  с перенесенными данными.
Расширение не может притендовать на что-либо. 
Цель - повысить понимание работы иструмента и работы с инструментом. 
Описание реализации в пункте `migrate:split`. Все перед ним - простое описание процесса миграции

### migrate:data

#### Игнорирование таблицы
для source таблиц (т.е. из М1 БД):
```xml
<source>
    <document_rules>
        <ignore>
            <document>migration_ignore_me_table</document>
        </ignore>
    </document_rules>
</source>
```

для destination таблиц т.е. из М2 БД):
```xml
<destination>
    <document_rules>
        <ignore>
            <document>migration_ignore_me_table</document>
        </ignore>
    </document_rules>
</destination>
```
----------------------------------
#### Игнорирование типов данных (в таблице, из которой  выполняется трансфер данных)
```xml
<source>
    <document_rules>
        <ignore>
           <datatype>migration_change_field_table.lastname_change_field_type</datatype>
        </ignore>
    </document_rules>
</source>
```
----------------------------------
#### Игнорирование переносимых полей
tool позволяет игонорировать (не переносить) данные также из полей. К примеру если необходимо, чтобы данные из М1 не перенеслись:
```xml
<source>
    <fields_rules>
        <ignore>
            <field>sales_flat_invoice.base_customer_balance_amount</field>
        </ignore>
    </fields_rules>
</source>
```
... или если необходимо, чтобы данные не перенеслись в поле  таблицы М2:
```xml
<destination>
    <fields_rules>
        <ignore>
            <field>admin_user.interface_locale</field>
        </ignore>
    </fields_rules>
</destination>
```

----------------------------------
#### Изменение данных переносимых из М1 в М2
Изменение данных при переносе - специальная операция которая не выпоняется data-migration-tool. Для изменения переносимых данных необходимо hadnler. 
```xml
<source>
    <field_rules>
        <transform>
            <field>migration_change_field_table.modify_content</field>
            <handler class="\Migration\Handler\CustomizeTransferredData\CustomizeTransferredData">
                <param name="postFix" value="_m2"/>
            </handler>
        </transform>
    </field_rules>
</source>
```

Декларируется handler в блоке `<transform></transform>` в одноименном теге `<handler></handler>`. В атрибуте  `class` указывется namespace обработчика. 

В теге `<param/>` указываются параметры, которые будут переданы в данный обработчик.

----------------------------------

#### Перенос данных в поле с измененым именем

Для переноса данных tool нужно подсказать, из какого поля из source и в какое поле с новым именем в destination перенести данные. Пример самодостаточен.
```xml
<source>
    <field_rules>
        <move>
            <field>migration_change_field_table.name_change_field_name</field>
            <to>migration_change_field_table.name_change_field_name_m2</to>
        </move>
    </field_rules>
</source>
```
----------------------------------

#### Перенос данных между таблицами с разными именами

При таком переносе таблицы обязаными иметь одинаковую страктуру. Перенос осуществялется таким путем:
```xml
<documnt_rules>
    <source>
        <rename>
            <document>migration_change_my_name_table</document>
            <to>migration_change_my_name_table_m2</to>
        </rename>
    </source>
</document_rules>
```
----------------------------------

###### Промежуточный вывод

tool позволяет выпонить такие действия при переносе данных:
1) Изменить поле-получатель в таблице М2
2) Блокировать перенос в поле в М2 таблице
3) Игнорировать тип переносимых данных из М1 в М2
4) Изменить таблицу-получатель данных в М2
5) Изменить сами переносимые данные ( `transform` )
6) Игонорировать поле при переносе из М1 таблице
7) Игнорировать таблицу в М1 при переносе данных

Для выпонения каких-то других процессов (к примеру, split таблиц или combine таблиц)  необходимо писать `custom step`. Иного пути нет. Или создавать свой отдельный обработчик.

----------------------------------

#### custom step
К примеру, нужно обьединить несколько таблиц. Для работы необходимо написать свои обработчики combine-процесса. Обработчики указываются в `config.xml` в теге `<steps mode="data">` (примеры реализации классов есть в прикрепленном коде (пути === namespace)):
 ```xml
<step title="Combine Step">
    <integrity>Migration\Step\CustomizeTransferredData\Combine\Integrity</integrity>
    <data>Migration\Step\CustomizeTransferredData\Combine\Data</data>
    <volume>Migration\Step\CustomizeTransferredData\Combine\Volume</volume>
</step>
```
 
----------------------------------

#### migrate:split
## SPLIT таблиц
Именно об этой части говорится в "прологе".
Для реализации было написано свое расширение `data-migration-tool`. Это расширение включает:
1) создание команды для запуска;
2) файл настройки;
3) обработчики;
4) сообщение об ошибках;
5) etc;

`Расширение не может считаться законченным, т.к. не имеет написаных тест-кейсов и не было должным образом протестировано (тестирование проводилось только на версии 1.9.3.10 М1). Тестировалось только локально. Расширение 'сырое и не для продакшина', а также работает сейчас только с версией magento 2.3` 
 
В прикрепленных файлах уже имеется пример реализации `split process`. 

##### Настройка и этапность

1)| в файле `config.xml` создать новый степ с модом `split`. Реализованный пример:
```xml
<steps mode="split">
    <step title="Split Step">
        <integrity>Migration\Step\CustomizeTransferredData\Split\Integrity</integrity>
        <data>Migration\Step\CustomizeTransferredData\Split\Data</data>
        <volume>Migration\Step\CustomizeTransferredData\Split\Volume</volume>
    </step>
</steps>
``` 
Пример находится в файле `config.xml.dist`. значения тегов `<integrity/>, <data/>, <volume/>` не меняются - это основные обработчики split-процесса.

2)l в файле `config.xml` раскомментировать строчку, которая находится в теге `<split_file></split_file>`. В данном теге находится путь к файлу, в котором происходит декларирование условий переноса данных (т.е. что перенести/проигнорировать/etc). Подход такой же, как и у самой tool. Также нужно убедиться, что путь в теге указывает на файл `split.xml`, который находиться в той версии  M1 c которой происходит миграция.

3)| реализованный пример `split.xml` находится также в прикрепленных файлах (в директории 1.9.3.10 M1).

4)| `split.xml` ссылается на свой namespace находящийся на одном уровне с директориями `opensource-to-...`. namespace - `split_combine_process.xsd`

Это единственные этапы по подготовке.

#### split - процесс

файл  `split.xml` структурно состоит из трех частей:

1) `settings`;

2) `from`;

3) `transfer`;

##### `<SETTINGS />`

Отвечает за настройки для проведения миграции. Содержит два основных sub-тега:
1) `source` - настройки для данных/полей переносимых из М1
2) `destination` - настройки для данных/полей в БД М2, т.е. куда переносятся данные

каждый из этих тегов имеет свои sub-теги.

##### Игнорирование полей

Если нужно не переносить с М1:
```xml
 <source>
    <ignore>
        <field>migration_split_me_table.is_new</field>
        <field>migration_split_me_table.value</field>
    </ignore>
</source>
   
```  
Если нужно не переносить в М2:

```xml
 <destination>
    <ignore>
        <field>migration_split_me_table_person.to_move_test</field>
    </ignore>
</destination>
```

##### Игнорирование переносимых типов данных

Игнорирование типов данных выполняется для таблиц, которые ожидают данные. Пример:
```xml
<destination>
    <ignore>
        <datatype>migration_split_me_table_person.account_id_split_1</datatype>
        <datatype>migration_split_me_table_person.name_split_1</datatype>
    </ignore>
</destination>
```
##### Изменение поля получателя данных в таблице M2

Данная настройка аналогична `migrate:data` процессу. Пример:
```xml
<settings>
    <source>
        <move>
            <field>migration_split_me_table.id</field>
            <to>migration_split_me_table_person.to_move_test</to>
        </move>
    </source>
</settings>    
``` 
Данные из поля `id`  таблицы `migration_split_me_table` в М1 будут перенесены в поле `to_move_test` таблицы `migration_split_me_table_person` в М2. 

##### `<FROM />`

 Здесь указывается откуда (из какой таблицы в М1) берутся данные для переноса.
 
 `ВАЖНО:`
  В данном теге (на текущий момент разработки) можно указать только один документ для split-процесса.
  Пример настройки:
  ```xml
 <from>
    <documents>
        <document>migration_split_me_table</document>
    </documents>
</from>
```
Данные будут переносится из таблицы М1 `migration_split_me_table`.
  
##### `<TRANSFER />`
Содержит инфо о том в какие таблицы будут переносится данные. Возможно указать множество таблиц. Пример:
```xml
<transfer>
    <document>migration_split_me_table_main</document>
    <document>migration_split_me_table_person</document>
</transfer>
```
Данные будут перенесены в таблице в М2 `migration_split_me_table_main`  и `migration_split_me_table_person`.
По этим таблицам можно провести свои настройки в теге `destination`. Детальное инфо выше.

-------------------------
##### Что еще?

Таблицы, которые будут обрабатываться в процессе `migrate:split` на этапе `migrate:data` целесообразно проигнорировать. Данное расширеение акутально для проектов `opensource-to-opensource`

##### Ошибки

Расширение имеет такие ошибки:
1) ошибки уровня `Integrity`
2) ошибки уровня `Data`
3) ошибки уровня `Volume`
4) оибки уровня  `MYSQL::Log`

Ошибки аналогичны основному коду `tool`.

##### START

Запуск комманд:

```textmate
 # An example of starting the process in the root directory
 # run one of the commands (example):

 php bin/magento migrate:data       vendor/magento/data-migration-tool/etc/opensource-to-opensource/1.9.3.10/config.xml
 php bin/magento migrate:settings   vendor/magento/data-migration-tool/etc/opensource-to-opensource/1.9.3.10/config.xml
 php bin/magento migrate:split      vendor/magento/data-migration-tool/etc/opensource-to-opensource/1.9.3.10/config.xml
```
 ##### epilogue
 
 Написанное расширение не может перекрыть всех возможных кейсов связанных с split. Много нюансов.
 Какие кейсы расширение способно обработать:
 1) если перенос осуществляется только из одной таблицы
 2) если данные при переносе не должны быть трансформированы (однако это можно реализовать)
 3) Если внешние ключи для таблиц (в которые переносятся данные) нужно взять из сторонних таблиц (т.е. не из переносимой или в которые переносятся). Расширение данный кейс не перекроет. Однако этот функционал можно создать 


