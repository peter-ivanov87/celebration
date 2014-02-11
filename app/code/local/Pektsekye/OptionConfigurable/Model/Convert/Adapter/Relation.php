<?php

class Pektsekye_optionconfigurable_Model_Convert_Adapter_Relation extends Mage_Dataflow_Model_Convert_Parser_Csv
{

    public function parse()
    {     
        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');

        $fDel = $this->getVar('delimiter', ',');
        $fEnc = $this->getVar('enclose', '"');
        if ($fDel == '\t') {
            $fDel = "\t";
        }
        
        $storeIds = $this->getStoreIds();        
        
        $relationModel  = Mage::getModel('optionconfigurable/relation');
        $attributeModel = Mage::getModel('optionconfigurable/attribute');
        $aoptionModel   = Mage::getModel('optionconfigurable/aoption');        
		    $optionModel    = Mage::getModel('optionconfigurable/option');
		    $valueModel     = Mage::getModel('optionconfigurable/value');

        $batchModel = $this->getBatchModel();
        $batchIoAdapter = $this->getBatchModel()->getIoAdapter();        

        $batchIoAdapter->open(false);
        
        $firstRow = $batchIoAdapter->read(true, $fDel, $fEnc);
        if (empty($firstRow[0]) || $firstRow[0] != 'option_ids' || empty($firstRow[1])){
          $this->addException(Mage::helper('optionconfigurable')->__('Option ids data was not found in the first row. Stop import process.'), Mage_Dataflow_Model_Convert_Exception::FATAL); 
          return;      
        }
         
        $translatedIds = $relationModel->getTranslatedIds(Zend_Json::decode($firstRow[1]));

        $data = array();
        $countRows = 0;
        $skippedRows = 0;        
        while (($csvData = $batchIoAdapter->read(true, $fDel, $fEnc)) !== false) {
          if (count($csvData) == 1 && $csvData[0] === null) {
              continue;
          }
                 
          if ($skippedRows > 100){
           $this->addException(Mage::helper('optionconfigurable')->__('Too many rows to skip. Stop import process.'), Mage_Dataflow_Model_Convert_Exception::FATAL);
           break;
          }
          
          $countRows++;          

          if (empty($csvData[0])){
            $this->addException(Mage::helper('optionconfigurable')->__('Skip import row, required field "%s" is not defined', 'product_sku'), Mage_Dataflow_Model_Convert_Exception::FATAL);
            $skippedRows++;
            continue;        
          }    
          
          $productId = Mage::getModel('catalog/product')->getIdBySku($csvData[0]);

          if ($productId == null){      
            $this->addException(Mage::helper('optionconfigurable')->__('Skip import row, the product with SKU "%s" does not exist', $csvData[0]), Mage_Dataflow_Model_Convert_Exception::FATAL);
            $skippedRows++;
            continue;        
          }         
          
          if (empty($csvData[1])){
            $this->addException(Mage::helper('optionconfigurable')->__('Skip import row, required field "%s" is not defined', 'relation_data'), Mage_Dataflow_Model_Convert_Exception::FATAL);
            $skippedRows++;
            continue;        
          }           
  
          $data = Zend_Json::decode($csvData[1]);
          
          if (isset($data['relations'])){
       
            $relationModel->saveCsvRelationData($productId, $data['relations'], $translatedIds);
          
            if (isset($data['attributes']))			
              $attributeModel->saveCsvAttributes($productId, $data['attributes'], $translatedIds, $storeIds);

            if (isset($data['aoptions']))			
              $aoptionModel->saveCsvAoptions($productId, $data['aoptions'], $translatedIds, $storeIds);
              
            if (isset($data['options']))			
              $optionModel->saveCsvOptions($productId, $data['options'], $translatedIds, $storeIds); 
              
            if (isset($data['values']))			
              $valueModel->saveCsvValues($productId, $data['values'], $translatedIds, $storeIds);              
                    
          }          
            
        }

        $importedRows = $countRows - $skippedRows;
          
        if ($skippedRows == 0)     
          $this->addException(Mage::helper('optionconfigurable')->__('Imported %d rows.',$countRows));
        else 
          $this->addException(Mage::helper('optionconfigurable')->__('Imported %d rows of %d',$importedRows,$countRows)); 
        
        return $this;

    }


    protected function getStoreIds()
    {
      $storeIds = array();
      $stores = Mage::app()->getStores(true, true);
      foreach ($stores as $code => $store) {
          $storeIds[$code] = $store->getId();
      }
      return $storeIds; 
    }	

	 
}
