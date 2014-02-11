<?php

class Pektsekye_OptionConfigurable_Model_Aoption extends Mage_Core_Model_Abstract
{	

    public function _construct()
    {
      parent::_construct();
      $this->_init('optionconfigurable/aoption');
    }

    public function getValues($productId, $storeId = 0)
    {        
      return $this->getResource()->getValues($productId, $storeId);                               
    }
 
 
    public function getAoptionsAllStores($productId)
    {      
      $data = $this->getResource()->getValues($productId, 0);
      foreach($data as $k => $r){
        $data[$k]['descriptions'] = $this->getResource()->getStoreDescriptions($productId, $r['aoption_id']);         
      }
      return $data;                          
    }  
    
        
    public function saveValues($productId, $storeId, $values)
    {
      return $this->getResource()->saveValues($productId, $storeId, $values);
    }
   
    
    
    public function saveCsvAoptions($productId, $aoptions, $tIds, $storeIds)
    {
      $aoptions = $this->translateCsvAoptions($aoptions, $tIds, $storeIds);      
      $this->saveValues($productId, 0, $aoptions);
    }
    
    
    
    public function translateCsvAoptions($aoptions, $tIds, $storeIds)
    {
      $t = 'a';
      $tAoptions = array();       
      foreach($aoptions as $id => $value){
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
          $tAoptions[$tId] = $tValue;
        }   
      } 
                
      return $tAoptions;
    }    
}
