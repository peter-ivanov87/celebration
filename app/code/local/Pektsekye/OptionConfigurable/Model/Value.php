<?php

class Pektsekye_OptionConfigurable_Model_Value extends Mage_Core_Model_Abstract
{	

    public function _construct()
    {
      parent::_construct();
      $this->_init('optionconfigurable/value');
    }
    
    
    
    public function getValues($productId, $storeId = 0)
    {        
      return $this->getResource()->getValues($productId, $storeId);                               
    }


    
    public function getValuesAllStores($productId)
    {      
      $data = $this->getResource()->getValues($productId, 0);
      foreach($data as $k => $r){
        $data[$k]['descriptions'] = $this->getResource()->getStoreDescriptions($r['value_id']);         
      }
      return $data;                          
    }      
    
    public function saveValues($productId, $storeId, $values)
    {
      return $this->getResource()->saveValues($productId, $storeId, $values);
    }



    public function copyValueData($originalProduct, $currentProductId, $storeId, $tIds)
    {       
      $valueData = $this->getValues($originalProduct->getId(), $storeId);    
      if (count($valueData) == 0)
        return false;

      foreach($valueData as $k => $value){      
          if (is_null($value['store_description']))
            $valueData[$k]['scope'] = 1;  
      }

      $this->saveValues($currentProductId, $storeId, $this->translateIds($valueData, $tIds));
            
      return true;                    
    } 
    
    public function translateIds($values, $tIds)
    {     
      $tValues = array();       
      foreach ($values as $id => $value){
        if (isset($tIds[1][$id])){
          $tId = $tIds[1][$id];        
          $tValues[$tId] = $value;
        }   
      }                
      return $tValues;
    }         
        
        
    public function saveCsvValues($productId, $values, $tIds, $storeIds)
    {
      $values = $this->translateCsvValues($values, $tIds, $storeIds);   
      $this->saveValues($productId, 0, $values);
    }
    
    
    
    public function translateCsvValues($values, $tIds, $storeIds)
    {
      $t = 'o';
      $tValues = array();       
      foreach($values as $id => $value){
        if (isset($tIds[1][$t][$id])){
          $tId = $tIds[1][$t][$id];
          $tValue = $value;
          if (isset($value['descriptions'])){
            $tValue['descriptions'] = array();
            foreach($value['descriptions'] as $storeCode => $description){
              $storeId = isset($storeIds[$storeCode]) ? $storeIds[$storeCode] : 0;
              if (!isset($tValue['descriptions'][$storeId])){
                $tValue['descriptions'][$storeId] = $description;          
              }
            }         
          }                              
          $tValues[$tId] = $tValue;
        }   
      } 
                
      return $tValues;
    }          
}
