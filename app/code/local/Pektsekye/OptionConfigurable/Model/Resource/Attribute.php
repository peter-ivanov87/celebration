<?php

class Pektsekye_OptionConfigurable_Model_Resource_Attribute extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('optionconfigurable/attribute', 'attribute_id');
    }
  
  
    public function getAttributes($productId, $storeId)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('main_table'=>$this->getMainTable()), array('attribute_id','order','required','default','layout','popup'))
        ->joinLeft(array('default_option_note'=>$this->getTable('optionconfigurable/attribute_note')),
            '`default_option_note`.product_id=`main_table`.product_id AND `default_option_note`.attribute_id=`main_table`.attribute_id AND `default_option_note`.store_id=0',
            array('default_note'=>'note'))
        ->joinLeft(array('store_option_note'=>$this->getTable('optionconfigurable/attribute_note')),
            '`store_option_note`.product_id=`main_table`.product_id AND `store_option_note`.attribute_id=`main_table`.attribute_id AND '.$this->_getWriteAdapter()->quoteInto('`store_option_note`.store_id=?', $storeId),
            array('store_note'=>'note',
            'note'=>new Zend_Db_Expr('IFNULL(`store_option_note`.note,`default_option_note`.note)')))        
        ->where("main_table.product_id=?", $productId); 
        
      return $this->_getReadAdapter()->fetchAssoc($select);                                 
    } 
    
    
    public function getUsedAttributeOptionIds()
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('oc' => $this->getMainTable()), array())
        ->join(array('eav' => $this->getTable('eav/attribute')), 'eav.attribute_id = oc.attribute_id', array('attribute_code', 'attribute_id'))
        ->join(array('op' => $this->getTable('eav/attribute_option')), 'op.attribute_id = oc.attribute_id', 'option_id') 
        ->distinct(true)                
        ->order("sort_order"); 
        
      return $this->_getReadAdapter()->fetchAll($select);                                 
    }


    public function getStoreNotes($productId, $attributeId)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('cs' => $this->getTable('core/store')), 'code')        
        ->join(array('ocn' => $this->getTable('optionconfigurable/attribute_note')), 'ocn.store_id = cs.store_id', 'note') 
        ->where("product_id={$productId} AND attribute_id={$attributeId}"); 
               
      return $this->_getReadAdapter()->fetchPairs($select);                           
    }
    

    public function getAttributeOptionIds($codes)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('et' => $this->getTable('eav/entity_type')), array())      
        ->join(array('ea' => $this->getTable('eav/attribute')), 'ea.entity_type_id = et.entity_type_id', array('attribute_code', 'attribute_id'))
        ->join(array('op' => $this->getTable('eav/attribute_option')), 'op.attribute_id = ea.attribute_id', 'option_id') 
        ->where('attribute_code IN (?)', $codes)
        ->where("et.entity_type_code='catalog_product'")                         
        ->order("sort_order"); 
        
      return $this->_getReadAdapter()->fetchAll($select);                                 
    }


    public function saveAttributes($productId, $storeId, $attributes)
    {    
      $storeId = (int) $storeId;
      $read = $this->_getReadAdapter();
      $write = $this->_getWriteAdapter();
      
      $oldAttributes = $this->getAttributes($productId, $storeId);
            
      if (count($attributes) == 0)
        return;

      $data = array();          
      foreach($attributes as $attributeId => $attribute){  

        $attributeId = (int) $attributeId;        

        $order = 0;
        $required = 0;         
        $default = '';  
        if (isset($attribute['default'])){
          $order = (int) $attribute['order'];        
          $required = isset($attribute['required']) ? $attribute['required'] : 0;        
          $default = is_array($attribute['default']) ? implode(',', $attribute['default']) : $attribute['default'];
        } elseif (isset($oldAttributes[$attributeId])){
          $order = $oldAttributes[$attributeId]['order'];
          $required = $oldAttributes[$attributeId]['required'];                   
          $default = $oldAttributes[$attributeId]['default'];
        } 
                
        $note = '';    
        $layout = '';  
        $popup = 0;       
        if (isset($attribute['note']) || isset($attribute['scope'])){ // option images tab loaded
          $note = isset($attribute['note']) ? $attribute['note'] : '';
          $layout = isset($attribute['layout']) ? $attribute['layout'] : '';  
          $popup = isset($attribute['popup']) ? $attribute['popup'] : 0;                   
        } elseif (isset($oldAttributes[$attributeId])){
          $note = $oldAttributes[$attributeId]['note'];
          $layout = $oldAttributes[$attributeId]['layout'];  
          $popup = $oldAttributes[$attributeId]['popup'];                  
        }      
             
        $data = array(
          'product_id'              => $productId, 
          'attribute_id'            => $attributeId, 
          'order'                   => $order, 
          'required'                => $required,
          'default'                 => $default,
          'layout'                  => $layout,   
          'popup'                   => $popup        
        );
        
        $statement = $read->select()
          ->from($this->getMainTable())
          ->where("product_id={$productId} AND attribute_id={$attributeId}");
          
        if ($read->fetchRow($statement)) {
            $write->update(
              $this->getMainTable(),
              $data,
              "product_id={$productId} AND attribute_id={$attributeId}"
            );
        } else {
          $write->insert($this->getMainTable(), $data);
        }

        if (isset($attribute['notes'])){ // csv import
          foreach($attribute['notes'] as $sId => $note){          
            $this->saveNote($note, $productId, $attributeId, $sId, false);
          }
        } else {        
          $scope = isset($attribute['scope']);
          $this->saveNote($note, $productId, $attributeId, $storeId, $scope); 
        }                  
      }        
                                  
    }
 
 
 	   protected function saveNote($note, $productId, $attributeId, $storeId, $scope)
    {
		    $read = $this->_getReadAdapter();
		    $write = $this->_getWriteAdapter();
		    $noteTable = $this->getTable('optionconfigurable/attribute_note');
		    		
        if (!$scope) {		
		      $statement = $read->select()
			      ->from($noteTable, array('note'))
			      ->where("product_id={$productId} AND attribute_id={$attributeId} AND store_id=0");
          $default = $read->fetchRow($statement);
		      if (!empty($default)) {
			      if ($storeId == '0' || $default['note'] == '') {
				      $write->update(
					      $noteTable,
						      array('note' => $note),
						      "product_id={$productId} AND attribute_id={$attributeId} AND store_id=0"
				      );
			      }
		      } else {
			      $write->insert(
				      $noteTable,
					      array(
							    'product_id' => $productId,						      
						      'attribute_id' => $attributeId,
						      'store_id' => 0,
						      'note' => $note
			      ));
		      }
        }
        
		    if ($storeId != '0' && !$scope) {
			    $statement = $read->select()
				    ->from($noteTable)
				    ->where("product_id={$productId} AND attribute_id={$attributeId} AND store_id=?", $storeId);

			    if ($read->fetchRow($statement)) {
				    $write->update(
					    $noteTable,
						    array('note' => $note),
						    $write->quoteInto("product_id={$productId} AND attribute_id={$attributeId} AND store_id=?", $storeId));
			    } else {
				    $write->insert(
					    $noteTable,
						    array(
							    'product_id' => $productId,						    
							    'attribute_id' => $attributeId,
							    'store_id' => $storeId,
							    'note' => $note
				    ));
			    }
		    } elseif ($scope){
            $write->delete(
                $noteTable,
                $write->quoteInto("product_id={$productId} AND attribute_id={$attributeId} AND store_id=?", $storeId)
            );		    
		    }
	}
	
	 
    
    public function copyAttributes($originalProductId, $currentProductId)
    { 
      $select = $this->_getWriteAdapter()->select()
          ->from($this->getMainTable(), array(new Zend_Db_Expr($currentProductId), 'attribute_id', 'order','required','default','layout','popup'))
          ->where('product_id = ?', $originalProductId);

      $insertSelect = $select->insertFromSelect($this->getMainTable());
      $this->_getWriteAdapter()->query($insertSelect);
      
      $noteTable = $this->getTable('optionconfigurable/attribute_note');
      $select = $this->_getWriteAdapter()->select()
          ->from($noteTable, array(new Zend_Db_Expr($currentProductId), 'attribute_id', 'store_id','note'))
          ->where('product_id = ?', $originalProductId);

      $insertSelect = $select->insertFromSelect($noteTable);
      $this->_getWriteAdapter()->query($insertSelect);      
     }     

}
