<?php

class Pektsekye_OptionConfigurable_Model_Resource_Relation extends Mage_Core_Model_Resource_Db_Abstract
{

    protected $_op_tables = array(
        'aoption_to_attribute' => array('optionconfigurable/aoption_to_attribute', 'aoption_id', 'children_attribute_id'),    
        'aoption_to_aoption'   => array('optionconfigurable/aoption_to_aoption', 'aoption_id', 'children_aoption_id'),
        'aoption_to_option'    => array('optionconfigurable/aoption_to_option', 'aoption_id', 'children_option_id'),      
        'aoption_to_value'     => array('optionconfigurable/aoption_to_value', 'aoption_id', 'children_value_id'), 
        'value_to_attribute'   => array('optionconfigurable/value_to_attribute', 'value_id', 'children_attribute_id'),  
        'value_to_aoption'     => array('optionconfigurable/value_to_aoption', 'value_id', 'children_aoption_id'),
        'value_to_option'      => array('optionconfigurable/value_to_option', 'value_id', 'children_option_id'),      
        'value_to_value'       => array('optionconfigurable/value_to_value', 'value_id', 'children_value_id')
        );


    public function _construct()
    {
      $this->_init('optionconfigurable/aoption_to_aoption', 'aoption_id');
    }
    
    
    public function getRelations($productId)
    { 
      $data = array();
      foreach($this->_op_tables as $key => $t)
        $data[$key] = $this->_getRelation($productId, $t[0], $t[1], $t[2]);
      return $data;
    }   


    public function _getRelation($productId, $tableKey, $field, $childrenField)
    {
      $select = $this->_getReadAdapter()->select()
        ->from($this->getTable($tableKey), array('id'=>$field, 'cid'=>$childrenField))
        ->where("product_id=?", $productId);
      return $this->_getReadAdapter()->fetchAll($select);      
    }     
        
        
    public function saveRelationsData($productId, $data)
    {    
      foreach($this->_op_tables as $key => $t)
        $this->_save($productId, $data[$key], $t[0], $t[1], $t[2]);    
    } 


    public function _save($productId, $relation, $tableKey, $field, $childrenField)
    { 
      $table = $this->getTable($tableKey);
              
      $this->_getWriteAdapter()->delete($table, array("product_id=?" => $productId));

      $data = array();          
      foreach($relation as $value => $children){         
        foreach($children as $id)
          $data[] = array('product_id' => $productId, $field => $value, $childrenField => $id);       
      }
      
      if (count($data) > 0)
        $this->_getWriteAdapter()->insertMultiple($table, $data);                                         
    }
    
    
    public function copyRelation($originalProductId, $currentProductId, $relationType)
    {             
      $table = $this->getTable($this->_op_tables[$relationType][0]);
      $field = $this->_op_tables[$relationType][1]; 
      $childrenField = $this->_op_tables[$relationType][2];
      
      $select = $this->_getWriteAdapter()->select()
          ->from($table, array(new Zend_Db_Expr($currentProductId), $field, $childrenField))
          ->where('product_id = ?', $originalProductId);

      $this->_getWriteAdapter()->query($select->insertFromSelect($table));
    }       


    public function getUsedPoductSkus()
    {
      $selects = array();
      foreach($this->_op_tables as $t)
        $selects[] = $this->_getReadAdapter()->select()
        ->from(array('rel' => $this->getTable($t[0])), 'product_id')
        ->join(array('cat' => $this->getTable('catalog/product')), 'cat.entity_id = rel.product_id', 'sku')
        ->distinct(true);
    
      $select = $this->_getReadAdapter()->select()
        ->union($selects, Zend_Db_Select::SQL_UNION); 
               
      return $this->_getReadAdapter()->fetchPairs($select);   
    }

}
