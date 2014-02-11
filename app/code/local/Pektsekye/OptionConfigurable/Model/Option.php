<?php

class Pektsekye_OptionConfigurable_Model_Option extends Mage_Core_Model_Abstract
{	

    public function _construct()
    {
      parent::_construct();
      $this->_init('optionconfigurable/option');
    }


    public function getOptions($productId, $storeId = 0)
    {        
      return $this->getResource()->getOptions($productId, $storeId);                            
    } 


    public function getOptionsAllStores($productId)
    {   
    
      $data = $this->getResource()->getOptions($productId, 0);
      foreach($data as $k => $r){
        $data[$k]['notes'] = $this->getResource()->getStoreNotes($r['option_id']);         
      }
      return $data;                          
    }


    public function saveOptions($productId, $storeId, $options)
    {
      return $this->getResource()->saveOptions($productId, $storeId, $options);
    }
    
    
    public function saveCustomOptionsOrder($options)
    {
      foreach($options as $id => $value){
        if (isset($value['order']) && $value['order'] != $value['previous_order']) 
          $this->getResource()->saveCustomOptionOrder((int) $id, (int) $value['order']);  
      }
    }    
    
     
    public function saveCsvOptions($productId, $options, $ctIds, $storeIds)
    {
      $t = 'o';
      $tIds = array(    
        0 => isset($ctIds[0][$t]) ? $ctIds[0][$t] : array(),
        1 => isset($ctIds[1][$t]) ? $ctIds[1][$t] : array()    
      );
      $this->saveOptions($productId, 0, $this->translateIds($options, $tIds, $storeIds));    
    }       
    

    public function translateIds($options, $tIds, $storeIds)
    {     
      $tOptions = array();       
      foreach($options as $id => $value){
        if (isset($tIds[0][$id])){
          $tId = $tIds[0][$id];
          $tValue = $value;          
          $tValue['default'] = array();        
          if (isset($value['default'])){
            $vIds = explode(',', $value['default']);
            foreach($vIds as $vId){
              if (isset($tIds[1][$vId]))
                $tValue['default'][] = $tIds[1][$vId];              
            }          
          }
          if (isset($value['notes'])){
            $tValue['notes'] = array();
            foreach($value['notes'] as $storeCode => $note){
              $storeId = isset($storeIds[$storeCode]) ? $storeIds[$storeCode] : 0;
              if (!isset($tValue['notes'][$storeId])){
                $tValue['notes'][$storeId] = $note;          
              }
            }         
          }                    
          $tOptions[$tId] = $tValue;
        }   
      }                
      return $tOptions;
    } 
    
    
    public function importOptions($originalProduct, $currentProductId)
    {     
      $originalOptions = $originalProduct->getOptions();
      if (count($originalOptions) == 0)
        return false;
        
      $currentOptions = Mage::getModel('catalog/product')->load($currentProductId)->getOptions(); 
      if (count($currentOptions) > 0)
        return false;
        
      Mage::getModel('catalog/product_option')->duplicate($originalProduct->getId(), $currentProductId);
                            
      $this->getResource()->updateProduct($currentProductId, $originalProduct->getRequiredOptions());      
      
      return $this->copyOptionData($originalProduct, $currentProductId);
    }    
    
    
    public function copyOptionData($originalProduct, $currentProductId)
    {  
      $storeIds = (array) $originalProduct->getStoreIds();

      array_unshift($storeIds, 0);
 
      $tIds = $this->getTranslatedIds($originalProduct, $currentProductId);  
       
      foreach ($storeIds as $storeId) {

        $optionData = $this->getOptions($originalProduct->getId(), $storeId);    
        if (count($optionData) == 0)
          return false;

        foreach($optionData as $k => $option){      
          if (is_null($option['store_note']))
            $optionData[$k]['scope'] = 1;  
        }

        $this->saveOptions($currentProductId, $storeId ,$this->translateIds($optionData, $tIds, array()));
  
        Mage::getModel('optionconfigurable/value')->copyValueData($originalProduct, $currentProductId, $storeId, $tIds);
     
      }

      return true;                    
    } 
    
    
    public function getTranslatedIds($originalProduct, $currentProductId)
    {    
      $currentProduct = Mage::getModel('catalog/product')->load($currentProductId);
          
      $tIds = array();      
      $newOptions = array_values($currentProduct->getOptions());                     
      foreach (array_values($originalProduct->getOptions()) as $k => $option) {
        $tIds[0][$option->getOptionId()] = $newOptions[$k]->getOptionId();
        $newValues = array_values($newOptions[$k]->getValues());
        foreach (array_values($option->getValues()) as $kk => $value)
          $tIds[1][$value->getOptionTypeId()] = $newValues[$kk]->getOptionTypeId();                  
      }      
      return $tIds;
    }       
}




