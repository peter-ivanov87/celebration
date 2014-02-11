<?php

class Pektsekye_OptionConfigurable_Model_Resource_Aoption extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('optionconfigurable/aoption', 'aoption_id');
    }
  
  
    public function getValues($productId, $storeId)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('main_table' => $this->getMainTable()), array('aoption_id','image'))
        ->join(array('default_aoption_description'=>$this->getTable('optionconfigurable/aoption_description')),
            '`default_aoption_description`.product_id=`main_table`.product_id AND `default_aoption_description`.aoption_id=`main_table`.aoption_id AND `default_aoption_description`.store_id=0',
            array('default_description'=>'description'))
        ->joinLeft(array('store_aoption_description'=>$this->getTable('optionconfigurable/aoption_description')),
            '`store_aoption_description`.product_id=`main_table`.product_id AND `store_aoption_description`.aoption_id=`main_table`.aoption_id AND '.$this->_getWriteAdapter()->quoteInto('`store_aoption_description`.store_id=?', $storeId),
            array('store_description'=>'description',
            'description'=>new Zend_Db_Expr('IFNULL(`store_aoption_description`.description,`default_aoption_description`.description)')))      
        ->where("main_table.product_id=?", $productId);  
               
      return $this->_getReadAdapter()->fetchAssoc($select);                                 
    }
 
    
    public function getStoreDescriptions($productId, $aoptionId)
    {        
      $select = $this->_getReadAdapter()->select()
        ->from(array('cs' => $this->getTable('core/store')), 'code')        
        ->join(array('obd' => $this->getTable('optionconfigurable/aoption_description')), 'obd.store_id = cs.store_id', 'description') 
        ->where("product_id={$productId} AND aoption_id={$aoptionId}"); 
               
      return $this->_getReadAdapter()->fetchPairs($select);                           
    }
        
      
    public function saveValues($productId, $storeId, $values)
    {         
      $storeId = (int) $storeId;
      $read = $this->_getReadAdapter();
      $write = $this->_getWriteAdapter();

      if (count($values) == 0)
        return;
        
      foreach($values as $aoptionId => $aoption){
        $aoptionId = (int) $aoptionId;
        $image = $aoption['image'];
        
        if (isset($aoption['image_json'])){
          $newImage = '';
          $imageInfo = array();          
          if ($aoption['image_json'] != '') {			
            $imageInfo = Zend_Json::decode($aoption['image_json']);
            if (isset($imageInfo['file'])){		
              $newImage = $this->_moveImageFromTmp($imageInfo['file']);
            }	
			    }
          if (isset($imageInfo['file']) || !isset($imageInfo['url']))	
            $image = $newImage;
        }	
        			                                       
        $data = array(
          'product_id'=> $productId, 
          'aoption_id' => $aoptionId,      
          'image'     => $image        
        );         
            
        $statement = $read->select()
          ->from($this->getMainTable())
          ->where("product_id={$productId} AND aoption_id={$aoptionId}");

        if ($read->fetchRow($statement)) {
            $write->update(
              $this->getMainTable(),
              $data,
              "product_id={$productId} AND aoption_id={$aoptionId}"
            );
        } else {
          $write->insert($this->getMainTable(), $data);
        }
        
        if (isset($aoption['descriptions'])){ // csv import
          foreach($aoption['descriptions'] as $sId => $description){          
            $this->saveDescription($description, $productId, $aoptionId, $sId, false);
          }
        } else {        
          $description = isset($aoption['description']) ? $aoption['description'] : '';           
          $scope = isset($aoption['scope']);
          $this->saveDescription($description, $productId, $aoptionId, $storeId, $scope);  
        }          
      }

                           
    }
 
 
 	  protected function saveDescription($description, $productId, $aoptionId, $storeId, $scope)
    {
		    $read = $this->_getReadAdapter();
		    $write = $this->_getWriteAdapter();
		    $descriptionTable = $this->getTable('optionconfigurable/aoption_description');
		    		
        if (!$scope) {		
		      $statement = $read->select()
			      ->from($descriptionTable, array('description'))
			      ->where("product_id={$productId} AND aoption_id={$aoptionId} AND store_id=0");
          $default = $read->fetchRow($statement);
		      if (!empty($default)) {
			      if ($storeId == '0' || $default['description'] == '') {
				      $write->update(
					      $descriptionTable,
						      array('description' => $description),
						      "product_id={$productId} AND aoption_id={$aoptionId} AND store_id=0"
				      );
			      }
		      } else {
			      $write->insert(
				      $descriptionTable,
					      array(
						      'product_id' => $productId,					      
						      'aoption_id' => $aoptionId,
						      'store_id' => 0,
						      'description' => $description
			      ));
		      }
        }
        
		    if ($storeId != '0' && !$scope) {
			    $statement = $read->select()
				    ->from($descriptionTable)
				    ->where("product_id={$productId} AND aoption_id={$aoptionId} AND store_id=?", $storeId);

			    if ($read->fetchRow($statement)) {
				    $write->update(
					    $descriptionTable,
						    array('description' => $description),
						    $write->quoteInto("product_id={$productId} AND aoption_id={$aoptionId} AND store_id=?", $storeId));
			    } else {
				    $write->insert(
					    $descriptionTable,
						    array(
						      'product_id' => $productId,							    
							    'aoption_id' => $aoptionId,
							    'store_id' => $storeId,
							    'description' => $description
				    ));
			    }
		    } elseif ($scope){
            $write->delete(
                $descriptionTable,
                $write->quoteInto("product_id={$productId} AND aoption_id={$aoptionId} AND store_id=?", $storeId)
            );		    
		    }
	}  
	
	
    public function copyAoptions($originalProductId, $currentProductId)
    { 
      $select = $this->_getWriteAdapter()->select()
          ->from($this->getMainTable(), array(new Zend_Db_Expr($currentProductId), 'aoption_id','image'))
          ->where('product_id = ?', $originalProductId);

      $insertSelect = $select->insertFromSelect($this->getMainTable());
      $this->_getWriteAdapter()->query($insertSelect);
      
      $descriptionTable = $this->getTable('optionconfigurable/aoption_description');
      $select = $this->_getWriteAdapter()->select()
          ->from($descriptionTable, array(new Zend_Db_Expr($currentProductId), 'aoption_id', 'store_id','description'))
          ->where('product_id = ?', $originalProductId);

      $insertSelect = $select->insertFromSelect($descriptionTable);
      $this->_getWriteAdapter()->query($insertSelect);      
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
