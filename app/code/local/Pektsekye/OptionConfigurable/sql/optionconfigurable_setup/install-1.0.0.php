<?php


/** @var $installer Pektsekye_OptionConfigurable_Model_Resource_Setup */
$installer = $this;

/**
 * Prepare database for tables setup
 */
$installer->startSetup();


$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/attribute'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/attribute')) 
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
        ), 'Attribute ID')  
    ->addColumn('order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,        
        'default'   => '0',
        ), 'Order')
    ->addColumn('required', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,        
        'default'   => '0',
        ), 'Attribute Required') 
    ->addColumn('default', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Default')           
    ->addForeignKey($installer->getFkName('optionconfigurable/attribute', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                              
    ->addForeignKey($installer->getFkName('optionconfigurable/attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Attribute Table');
$installer->getConnection()->createTable($table);


$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/option'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/option'))  
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')       
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Option ID')          
    ->addColumn('default', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Default')                      
    ->addForeignKey($installer->getFkName('optionconfigurable/option', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)          
    ->addForeignKey($installer->getFkName('optionconfigurable/option', 'option_id', 'catalog/product_option', 'option_id'),
        'option_id', $installer->getTable('catalog/product_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Option Table');
$installer->getConnection()->createTable($table);



$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/aoption_to_attribute'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/aoption_to_attribute'))
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
    ->addColumn('children_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,        
        'default'   => '0',
        ), 'Children Attribute Id')        
    ->addIndex($installer->getIdxName('optionconfigurable/aoption_to_attribute', 'children_attribute_id'),
        'children_attribute_id')      
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_attribute', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)           
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_attribute', 'aoption_id', 'eav/attribute_option', 'option_id'),
        'aoption_id', $installer->getTable('eav/attribute_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE) 
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_attribute', 'children_attribute_id', 'eav/attribute', 'attribute_id'),
        'children_attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Attribute Option To Attribute Table');
$installer->getConnection()->createTable($table);


$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/aoption_to_aoption'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/aoption_to_aoption'))
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
    ->addColumn('children_aoption_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,        
        'default'   => '0',
        ), 'Children Attribute Option Id')        
    ->addIndex($installer->getIdxName('optionconfigurable/aoption_to_aoption', 'children_aoption_id'),
        'children_aoption_id')  
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_aoption', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)               
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_aoption', 'aoption_id', 'eav/attribute_option', 'option_id'),
        'aoption_id', $installer->getTable('eav/attribute_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE) 
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_aoption', 'children_aoption_id', 'eav/attribute_option', 'option_id'),
        'children_aoption_id', $installer->getTable('eav/attribute_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Attribute Option To Attribute Option Table');
$installer->getConnection()->createTable($table);



$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/aoption_to_option'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/aoption_to_option')) 
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
    ->addColumn('children_option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,        
        'default'   => '0',
        ), 'Children Option Id')        
    ->addIndex($installer->getIdxName('optionconfigurable/aoption_to_option', 'children_option_id'),
        'children_option_id')  
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_option', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)               
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_option', 'aoption_id', 'eav/attribute_option', 'option_id'),
        'aoption_id', $installer->getTable('eav/attribute_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE) 
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_option', 'children_option_id', 'catalog/product_option', 'option_id'),
        'children_option_id', $installer->getTable('catalog/product_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Attribute Option To Option Table');
$installer->getConnection()->createTable($table);



$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/aoption_to_value'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/aoption_to_value')) 
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
    ->addColumn('children_value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,        
        'default'   => '0',
        ), 'Children Option Value Id')        
    ->addIndex($installer->getIdxName('optionconfigurable/aoption_to_value', 'children_value_id'),
        'children_value_id')  
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_value', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)               
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_value', 'aoption_id', 'eav/attribute_option', 'option_id'),
        'aoption_id', $installer->getTable('eav/attribute_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE) 
    ->addForeignKey($installer->getFkName('optionconfigurable/aoption_to_value', 'children_value_id', 'catalog/product_option_type_value', 'option_type_id'),
        'children_value_id', $installer->getTable('catalog/product_option_type_value'), 'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Attribute Option To Option Value Table');
$installer->getConnection()->createTable($table);


$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/value_to_attribute'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/value_to_attribute'))  
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')       
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Option Value ID')  
    ->addColumn('children_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,        
        'default'   => '0',
        ), 'Children Attribute Id')        
    ->addIndex($installer->getIdxName('optionconfigurable/value_to_attribute', 'children_attribute_id'),
        'children_attribute_id')   
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_attribute', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)              
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_attribute', 'value_id', 'catalog/product_option_type_value', 'option_type_id'),
        'value_id', $installer->getTable('catalog/product_option_type_value'), 'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE) 
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_attribute', 'children_attribute_id', 'eav/attribute', 'attribute_id'),
        'children_attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Option Value To Attribute Table');
$installer->getConnection()->createTable($table);


$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/value_to_aoption'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/value_to_aoption')) 
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')        
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Option Value ID')  
    ->addColumn('children_aoption_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,        
        'default'   => '0',
        ), 'Children Attribute Option Id')        
    ->addIndex($installer->getIdxName('optionconfigurable/value_to_aoption', 'children_aoption_id'),
        'children_aoption_id')  
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_aoption', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)               
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_aoption', 'value_id', 'catalog/product_option_type_value', 'option_type_id'),
        'value_id', $installer->getTable('catalog/product_option_type_value'), 'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE) 
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_aoption', 'children_aoption_id', 'eav/attribute_option', 'option_id'),
        'children_aoption_id', $installer->getTable('eav/attribute_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Option Value To Attribute Option Table');
$installer->getConnection()->createTable($table);



$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/value_to_option'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/value_to_option')) 
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')        
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Option Value ID')  
    ->addColumn('children_option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,        
        'default'   => '0',
        ), 'Children Option Id')        
    ->addIndex($installer->getIdxName('optionconfigurable/value_to_option', 'children_option_id'),
        'children_option_id') 
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_option', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_option', 'value_id', 'catalog/product_option_type_value', 'option_type_id'),
        'value_id', $installer->getTable('catalog/product_option_type_value'), 'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE) 
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_option', 'children_option_id', 'catalog/product_option', 'option_id'),
        'children_option_id', $installer->getTable('catalog/product_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Option Value To Option Table');
$installer->getConnection()->createTable($table);



$installer->getConnection()->dropTable($installer->getTable('optionconfigurable/value_to_value'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('optionconfigurable/value_to_value')) 
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Product ID')        
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Option Value ID')  
    ->addColumn('children_value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,        
        'default'   => '0',
        ), 'Children Option Value Id')        
    ->addIndex($installer->getIdxName('optionconfigurable/value_to_value', 'children_value_id'),
        'children_value_id')  
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_value', 'product_id', 'catalog/product', 'entity_id'),
        'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)              
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_value', 'value_id', 'catalog/product_option_type_value', 'option_type_id'),
        'value_id', $installer->getTable('catalog/product_option_type_value'), 'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE) 
    ->addForeignKey($installer->getFkName('optionconfigurable/value_to_value', 'children_value_id', 'catalog/product_option_type_value', 'option_type_id'),
        'children_value_id', $installer->getTable('catalog/product_option_type_value'), 'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)                 
    ->setComment('OptionConfigurable Option Value To Option Value Table');
$installer->getConnection()->createTable($table);


$prefix = (string) Mage::getConfig()->getTablePrefix();
$odOptionTable = $prefix . 'optiondependent_option';
$odValueTable  = $prefix . 'optiondependent_value'; 

if ($this->tableExists($odOptionTable) && $this->tableExists($odValueTable)){
  $conn = $installer->getConnection();

  $optionId = array();
  $select = $conn->select()->from($odOptionTable);      
  foreach ($conn->fetchAll($select) as $r){
    $optionId[$r['product_id']][$r['row_id']] = $r['option_id'];   
  }

  $valueRows = $conn->fetchAll($conn->select()->from($odValueTable));
  
  $valueId = array();
  $productIds = array();            
  foreach ($valueRows as $r){
    $valueId[$r['product_id']][$r['row_id']] = $r['option_type_id'];
    $productIds[] = $r['product_id'];         
  }    
 
  $data = array(
      'aoption_to_attribute'=> array(),
      'aoption_to_option'   => array(),
      'value_to_attribute'  => array(),
      'value_to_option'     => array(),
      'aoption_to_aoption'  => array(),
      'aoption_to_value'    => array(),
      'value_to_aoption'    => array(),
      'value_to_value'      => array()     
  );    

  foreach ($valueRows as $r){
    $vId = $r['option_type_id'];
    $productId = $r['product_id'];
    if (!empty($r['children'])){
      foreach(explode(',', $r['children']) as $rowId){
        if (isset($optionId[$productId][$rowId])){
          $data['value_to_option'][$vId][] = $optionId[$productId][$rowId];         
        } elseif (isset($valueId[$productId][$rowId])){
          $data['value_to_value'][$vId][] = $valueId[$productId][$rowId];          
        }
      }
    }   
  }
  
  foreach ($productIds as $productId)   
    Mage::getResourceModel('optionconfigurable/relation')->saveRelationsData($productId, $data);   
}
/**
 * Prepare database after tables setup
 */
$installer->endSetup();
