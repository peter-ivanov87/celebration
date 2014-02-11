<?php

class Pektsekye_OptionConfigurable_Model_Resource_Value extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('optionconfigurable/value', 'value_id');
    }
  
  
    public function getValues($productId, $storeId)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('main_table' => $this->getMainTable()), array('value_id','image'))
        ->join(array('default_value_description'=>$this->getTable('optionconfigurable/value_description')),
            '`default_value_description`.value_id=`main_table`.value_id AND `default_value_description`.store_id=0',
            array('default_description'=>'description'))
        ->joinLeft(array('store_value_description'=>$this->getTable('optionconfigurable/value_description')),
            '`store_value_description`.value_id=`main_table`.value_id AND '.$this->_getWriteAdapter()->quoteInto('`store_value_description`.store_id=?', $storeId),
            array('store_description'=>'description',
            'description'=>new Zend_Db_Expr('IFNULL(`store_value_description`.description,`default_value_description`.description)')))      
        ->where("product_id=?", $productId);  
                       
      return $this->_getReadAdapter()->fetchAssoc($select);                                 
    }
    
      
    public function getStoreDescriptions($valueId)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('cs' => $this->getTable('core/store')), 'code')        
        ->join(array('obd' => $this->getTable('optionconfigurable/value_description')), 'obd.store_id = cs.store_id', 'description') 
        ->where('value_id=?', $valueId); 
               
      return $this->_getReadAdapter()->fetchPairs($select);                           
    }

      
    public function saveValues($productId, $storeId, $values)
    {                      
      $storeId = (int) $storeId;
      $read = $this->_getReadAdapter();
      $write = $this->_getWriteAdapter();

      foreach($values as $valueId => $value){  
        $valueId = (int) $valueId;
        $image = $value['image'];
        
        if (isset($value['image_json'])){
          $newImage = '';
          $imageInfo = array();          
          if ($value['image_json'] != '') {			
            $imageInfo = Zend_Json::decode($value['image_json']);
            if (isset($imageInfo['file'])){		
              $newImage = $this->_moveImageFromTmp($imageInfo['file']);
            }	
			    }
          if (isset($imageInfo['file']) || !isset($imageInfo['url']))	
            $image = $newImage;
        }	
        			                                     
        $data = array(
          'product_id'=> $productId, 
          'value_id' => $valueId,      
          'image'   => $image          
        );           
        
        $statement = $read->select()
          ->from($this->getMainTable())     
          ->where('value_id='.$valueId.' AND product_id=?',$productId);

        if ($read->fetchRow($statement)) {
            $write->update(
              $this->getMainTable(),
              $data,
              $write->quoteInto('value_id='.$valueId.' AND product_id=?', $productId)
            );
        } else {
          $write->insert($this->getMainTable(), $data);
        }

        if (isset($value['descriptions'])){ // csv import
          foreach($value['descriptions'] as $sId => $description){    
            $this->saveDescription($description, $valueId, $sId, false);
          }
        } else {        
          $description = isset($value['description']) ? $value['description'] : '';           
          $scope = isset($value['scope']);
          $this->saveDescription($description, $valueId, $storeId, $scope);
        }         
      }
                           
    }


 	    protected function saveDescription($description, $valueId, $storeId, $scope)
    {
		    $read = $this->_getReadAdapter();
		    $write = $this->_getWriteAdapter();
		    $descriptionTable = $this->getTable('optionconfigurable/value_description');
		    		
        if (!$scope) {		
		      $statement = $read->select()
			      ->from($descriptionTable, array('description'))
			      ->where('value_id = '.$valueId.' AND store_id = ?', 0);
          $default = $read->fetchRow($statement);
		      if (!empty($default)) {
			      if ($storeId == '0' || $default['description'] == '') {
				      $write->update(
					      $descriptionTable,
						      array('description' => $description),
						      $write->quoteInto('value_id='.$valueId.' AND store_id=?', 0)
				      );	      
			      }
		      } else {
			      $write->insert(
				      $descriptionTable,
					      array(
						      'value_id' => $valueId,
						      'store_id' => 0,
						      'description' => $description
			      ));
		      }
        }
        
		    if ($storeId != '0' && !$scope) {
			    $statement = $read->select()
				    ->from($descriptionTable)
				    ->where('value_id = '.$valueId.' AND store_id = ?', $storeId);

			    if ($read->fetchRow($statement)) {
				    $write->update(
					    $descriptionTable,
						    array('description' => $description),
						    $write->quoteInto('value_id='.$valueId.' AND store_id=?', $storeId));
			    } else {
				    $write->insert(
					    $descriptionTable,
						    array(
							    'value_id' => $valueId,
							    'store_id' => $storeId,
							    'description' => $description
				    ));
			    }
		    } elseif ($scope){
            $write->delete(
                $descriptionTable,
                $write->quoteInto('value_id = '.$valueId.' AND store_id = ?', $storeId)
            );		    
		    }
	  }   

    /**
     * Move image from temporary directory to normal
     *
     * @param string $file
     * @return string
     */
    protected function _moveImageFromTmp($file)
    {

        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->_getMadiaConfig()->getMediaPath($file));

        try {
            $ioObject->open(array('path'=>$destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path'=>$destDirectory));
        }

        if (strrpos($file, '.tmp') == strlen($file)-4) {
            $file = substr($file, 0, strlen($file)-4);
        }

        $destFile = dirname($file) . $ioObject->dirsep()
                  . Varien_File_Uploader::getNewFileName($this->_getMadiaConfig()->getMediaPath($file));

        $ioObject->mv(
          $this->_getMadiaConfig()->getTmpMediaPath($file),
          $this->_getMadiaConfig()->getMediaPath($destFile)
        );	

        return str_replace($ioObject->dirsep(), '/', $destFile);
    }
	
    /**
     * Retrive media config
     *
     * @return Mage_Catalog_Model_Product_Media_Config
     */
    protected function _getMadiaConfig()
    {
        return Mage::getSingleton('catalog/product_media_config');
    }	 
    
}
