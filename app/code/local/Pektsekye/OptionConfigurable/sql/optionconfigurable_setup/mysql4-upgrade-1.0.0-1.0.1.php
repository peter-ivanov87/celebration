<?php


/** @var $installer Pektsekye_OptionConfigurable_Model_Resource_Setup */
$installer = $this;

/**
 * Prepare database for tables setup
 */
$installer->startSetup();

$optionTable = $installer->getTable('optionconfigurable/option');

$installer->getConnection()->addColumn($optionTable, 'layout', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 256,
    'nullable' => false,
    'default' => '',
    'comment' => 'Layout'
));

$installer->getConnection()->addColumn($optionTable, 'popup', array(
    'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'unsigned'  => true,
    'nullable'  => false,        
    'default'   => '0',
    'comment' => 'Popup'
));



$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/option_note'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/option_note'))       
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Configurable Option ID')  
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')           
    ->addColumn('note', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Note')                   
    ->addForeignKey($installer->getFkName('optionconfigurable/option_note', 'option_id', 'optionconfigurable/option', 'option_id'),
        'option_id', $installer->getTable('optionconfigurable/option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                              
    ->addForeignKey($installer->getFkName('optionconfigurable/option_note', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Option Note Table');
$installer->getConnection()->createTable($table);


$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/value'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/value'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Option Value ID')       
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')        
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Image')                              
    ->addForeignKey($installer->getFkName('optionconfigurable/value', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                              
    ->addForeignKey($installer->getFkName('optionconfigurable/value', 'value_id', 'catalog/product_option_type_value', 'option_type_id'),
        'value_id', $installer->getTable('catalog/product_option_type_value'), 'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Option Value Table');
$installer->getConnection()->createTable($table);



$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/value_description'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/value_description'))       
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Option Value ID')  
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')           
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')                   
    ->addForeignKey($installer->getFkName('optionconfigurable/value_description', 'value_id', 'optionconfigurable/value', 'value_id'),
        'value_id', $installer->getTable('optionconfigurable/value'), 'value_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                              
    ->addForeignKey($installer->getFkName('optionconfigurable/value_description', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Option Value Description Table');
$installer->getConnection()->createTable($table);





$attributeTable = $installer->getTable('optionconfigurable/attribute');

$installer->getConnection()->addColumn($attributeTable, 'layout', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => 256,
    'nullable' => false,
    'default' => '',
    'comment' => 'Layout'
));

$installer->getConnection()->addColumn($attributeTable, 'popup', array(
    'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
    'unsigned'  => true,
    'nullable'  => false,        
    'default'   => '0',
    'comment' => 'Popup'
));


$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/attribute_note'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/attribute_note')) 
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')                                                   
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'OptionConfigurable Attribute ID')  
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')           
    ->addColumn('note', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Note')     
    ->addForeignKey($installer->getFkName('optionconfigurable/attribute_note', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                        
    ->addForeignKey($installer->getFkName('optionconfigurable/attribute_note', 'attribute_id', 'optionconfigurable/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('optionconfigurable/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                              
    ->addForeignKey($installer->getFkName('optionconfigurable/attribute_note', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Attribute Note Table');
$installer->getConnection()->createTable($table);



$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/aoption'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/aoption'))
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')     
    ->addColumn('aoption_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Attribute Option ID')              
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Image')                              
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                              
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption', 'aoption_id', 'eav/attribute_option', 'option_id'),
        'aoption_id', $installer->getTable('eav/attribute_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Attribute Option Table');
$installer->getConnection()->createTable($table);



$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/aoption_description'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/aoption_description'))    
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')       
    ->addColumn('aoption_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'OptionConfigurable Attribute Option ID')  
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')           
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_description', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                            
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_description', 'aoption_id', 'optionconfigurable/aoption', 'aoption_id'),
        'aoption_id', $installer->getTable('optionconfigurable/aoption'), 'aoption_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                              
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_description', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Attribute Option Description Table');
$installer->getConnection()->createTable($table);


$installer->endSetup();
