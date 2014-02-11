<?php

class Pektsekye_OptionConfigurable_Model_Resource_Option extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('optionconfigurable/option', 'option_id');
    }
  
  
    public function getOptions($productId, $storeId)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('main_table'=>$this->getMainTable()), array('option_id','default','layout','popup'))
        ->joinLeft(array('default_option_note'=>$this->getTable('optionconfigurable/option_note')),
            '`default_option_note`.option_id=`main_table`.option_id AND `default_option_note`.store_id=0',
            array('default_note'=>'note'))
        ->joinLeft(array('store_option_note'=>$this->getTable('optionconfigurable/option_note')),
            '`store_option_note`.option_id=`main_table`.option_id AND '.$this->_getWriteAdapter()->quoteInto('`store_option_note`.store_id=?', $storeId),
            array('store_note'=>'note',
            'note'=>new Zend_Db_Expr('IFNULL(`store_option_note`.note,`default_option_note`.note)')))         
        ->where("product_id=?", $productId); 
        
      return $this->_getReadAdapter()->fetchAssoc($select);                                 
    } 
    

    public function getUsedOptionIds()
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('oc' => $this->getMainTable()), 'product_id')
        ->join(array('cp' => $this->getTable('catalog/product')), 'cp.entity_id = oc.product_id', 'sku')        
        ->join(array('ca' => $this->getTable('catalog/product_option')), 'ca.product_id = cp.entity_id', 'option_id')
        ->join(array('ot' => $this->getTable('catalog/product_option_title')), 'ot.option_id = ca.option_id AND store_id=0', array())        
        ->joinLeft(array('va' => $this->getTable('catalog/product_option_type_value')), 'va.option_id = ca.option_id', array('value_id' => 'option_type_id'))
        ->joinLeft(array('vt' => $this->getTable('catalog/product_option_type_title')), 'vt.option_type_id = va.option_type_id', array())
        ->distinct(true)                          
        ->order(array('ca.sort_order','ot.title','va.sort_order','vt.title')); 
        
      return $this->_getReadAdapter()->fetchAll($select);                                 
    }


    public function getStoreNotes($optionId)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('cs' => $this->getTable('core/store')), 'code')        
        ->join(array('ocn' => $this->getTable('optionconfigurable/option_note')), 'ocn.store_id = cs.store_id', 'note') 
        ->where('option_id=?', $optionId); 
               
      return $this->_getReadAdapter()->fetchPairs($select);                           
    }
        

    public function getOptionIds($skus)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('cp' => $this->getTable('catalog/product')), array('product_sku' => 'sku','product_id' => 'entity_id'))       
        ->join(array('ca' => $this->getTable('catalog/product_option')), 'ca.product_id = cp.entity_id', 'option_id')
        ->join(array('ot' => $this->getTable('catalog/product_option_title')), 'ot.option_id = ca.option_id AND store_id=0', array())        
        ->joinLeft(array('va' => $this->getTable('catalog/product_option_type_value')), 'va.option_id = ca.option_id', array('value_id' => 'option_type_id'))
        ->joinLeft(array('vt' => $this->getTable('catalog/product_option_type_title')), 'vt.option_type_id = va.option_type_id', array())
        ->where("cp.sku IN (?)", $skus)                                   
        ->order(array('ca.sort_order','ot.title','va.sort_order','vt.title')); 
        
      return $this->_getReadAdapter()->fetchAll($select);                                 
    }    
    
    
    public function saveOptions($productId, $storeId, $options)
    {       
      $storeId = (int) $storeId;
      $read = $this->_getReadAdapter();
      $write = $this->_getWriteAdapter();
      
      $oldOptions = $this->getOptions($productId, $storeId);
            
      if (count($options) == 0)
        return;

      $data = array();          
      foreach($options as $optionId => $option){

        $default = '';
        if (isset($option['default'])){
          $default = is_array($option['default']) ? implode(',', $option['default']) : $option['default'];
        } elseif (isset($oldOptions[$optionId])){
          $default = $oldOptions[$optionId]['default'];
        }  
        
        $note = '';    
        $layout = '';  
        $popup = 0; 
        if (isset($option['note']) || isset($option['scope'])){ // option images tab loaded
          $note = isset($option['note']) ? $option['note'] : '';
          $layout = isset($option['layout']) ? $option['layout'] : '';  
          $popup = isset($option['popup']) ? $option['popup'] : 0;            
        } elseif (isset($oldOptions[$optionId])){
          $note = $oldOptions[$optionId]['note'];
          $layout = $oldOptions[$optionId]['layout'];  
          $popup = $oldOptions[$optionId]['popup'];  
        }
                
        $data = array(
          'product_id'=> $productId, 
          'option_id' => $optionId,      
          'default'   => $default,
          'layout'    => $layout,   
          'popup'     => $popup            
        );
        
        $statement = $read->select()
          ->from($this->getMainTable())
          ->where('option_id=?', $optionId);

        if ($read->fetchRow($statement)) {
            $write->update(
              $this->getMainTable(),
              $data,
              $write->quoteInto('option_id=?', $optionId)
            );
        } else {
          $write->insert($this->getMainTable(), $data);
        }

        if (isset($option['notes'])){ // csv import
          foreach($option['notes'] as $sId => $note){          
            $this->saveNote($note, $optionId, $sId, false);
          }
        } else {        
          $scope = isset($option['scope']);
          $this->saveNote($note, $optionId, $storeId, $scope); 
        }                  
      }
                                   
    }


 	  protected function saveNote($note, $optionId, $storeId, $scope)
    {
		    $read = $this->_getReadAdapter();
		    $write = $this->_getWriteAdapter();
		    $noteTable = $this->getTable('optionconfigurable/option_note');
		    		
        if (!$scope) {		
		      $statement = $read->select()
			      ->from($noteTable, array('note'))
			      ->where('option_id = '.$optionId.' AND store_id = ?', 0);
          $default = $read->fetchRow($statement);
		      if (!empty($default)) {
			      if ($storeId == '0' || $default['note'] == '') {
				      $write->update(
					      $noteTable,
						      array('note' => $note),
						      $write->quoteInto('option_id='.$optionId.' AND store_id=?', 0)
				      );
			      }
		      } else {
			      $write->insert(
				      $noteTable,
					      array(
						      'option_id' => $optionId,
						      'store_id' => 0,
						      'note' => $note
			      ));
		      }
        }
        
		    if ($storeId != '0' && !$scope) {
			    $statement = $read->select()
				    ->from($noteTable)
				    ->where('option_id = '.$optionId.' AND store_id = ?', $storeId);

			    if ($read->fetchRow($statement)) {
				    $write->update(
					    $noteTable,
						    array('note' => $note),
						    $write->quoteInto('option_id='.$optionId.' AND store_id=?', $storeId));
			    } else {
				    $write->insert(
					    $noteTable,
						    array(
							    'option_id' => $optionId,
							    'store_id' => $storeId,
							    'note' => $note
				    ));
			    }
		    } elseif ($scope){
            $write->delete(
                $noteTable,
                $write->quoteInto('option_id = '.$optionId.' AND store_id = ?', $storeId)
            );		    
		    }
	}
    
    
    public function saveCustomOptionOrder($optionId, $order)
    {       		   
      $this->_getWriteAdapter()->update($this->getTable('catalog/product_option'), array('sort_order' => $order), array('option_id = ?' => $optionId));                          
    }    

    
    public function updateProduct($productId, $hasRequired)
    {     	    			   
      $this->_getWriteAdapter()->update($this->getTable('catalog/product'), array('has_options' => 1, 'required_options' => (int) $hasRequired), array('entity_id = ?' => $productId));     
    }
}
