<?php

class Pektsekye_OptionConfigurable_Helper_Data extends Mage_Core_Helper_Abstract
{

  public function applySortOrder($productId, $options)
  {
      
      $product = Mage::getModel('catalog/product')->load($productId);
      
      if (!$product->getId())
        return $options;
        
      $hasOrder = false; 
                 
      if ($product->isConfigurable()){ 
 
        $attrOrder = array();
        $ocAttributes = Mage::getModel('optionconfigurable/attribute')->getAttributes($product->getId());          
        $attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);          
        foreach ($attributes as $attribute) {
          $id = (int) $attribute['attribute_id'];        
          $sortOrder = isset($ocAttributes[$id]['order']) ? (int) $ocAttributes[$id]['order'] : 0;         
          $attrOrder[$attribute['label']] = $sortOrder;
          $hasOrder |= $sortOrder > 0;            
        }  
      }
             
      $customOptions = $product->getOptions();         
      
      foreach ($options as $k => $option) {
      
        if ($option['value'] == 'not_selected'){
          unset($options[$k]);
          continue;
        }
       
                       
        if (isset($option['option_type'])){
          $id = (int) $option['option_id'];                 
          $options[$k]['sort_order'] = isset($customOptions[$id]) ? $customOptions[$id]->getSortOrder() : 0;              
        } else {
          $options[$k]['sort_order'] = isset($attrOrder[$option['label']]) ? $attrOrder[$option['label']] : 0;
        }
      }
      
     if ($hasOrder) 
        usort($options, array($this, "sortOptions"));

    return $options;   
  }



  public function hasVisibleRequiredOption($product)
  {     
    foreach ($this->getOptions($product) as $k => $option){
      if ($option['required'] == 1 && $option['hasNotDependentValues'])
        return true;    
    }      
    return false;          
  }  



  public function getOptions($product)
  {
    $options = array();      
    
    $rData = Mage::getModel('optionconfigurable/relation')->getRelationData($product); 
                                                     
    if ($product->isConfigurable()){
      $t = 'a';
      $ocAttributes = Mage::getModel('optionconfigurable/attribute')->getAttributes($product->getId());    	
      $attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);                
      foreach ($attributes as $attribute) {         
          $id = (int) $attribute['attribute_id'];
                                     
          $option = array(                
            'required' => isset($ocAttributes[$id]['required']) ? (int) $ocAttributes[$id]['required'] : 0              
          );
                         
          $hasNd = false;
          $hasNs = false;                        
          foreach ($attribute['values'] as $value){                     
              $hasNd |= !isset($rData['pVIdsByVId'][$t][$value['value_index']]);
              $hasNs |= $value['label'] == 'not_selected';                                                                                                                      
          }
          
          $option['required'] |= !$hasNs;
          $option['hasNotDependentValues'] = !isset($rData['pVIdsByOId'][$t][$id]) && $hasNd;                    
          $options[] = $option;          
      }        
    }
    
    $t = 'o';    
    $ocOptions = Mage::getModel('optionconfigurable/option')->getOptions($product->getId());            
    foreach ($product->getOptions() as $_option) {
        $id = (int) $_option->getOptionId(); 

        $option = array(
          'required' => $_option->getIsRequire() ? 1 : 0
        );
                
        $hasNd = false;                  
        foreach ($_option->getValues() as $value){
          if (!isset($rData['pVIdsByVId'][$t][$value->getOptionTypeId()])){
            $hasNd = true;
            break;
          }                
        }
        
        $option['hasNotDependentValues'] = !isset($rData['pVIdsByOId'][$t][$id]) && (count($_option->getValues()) == 0 || $hasNd);        
        $options[] = $option;
    }        
                         
    return $options;                               
  }
  
  
  
  public function sortOptions($o1, $o2)
  {
    $a = (int) $o1['sort_order'];
    $b = (int) $o2['sort_order'];	
    if ($a == $b)
        return 0;
    return ($a < $b) ? -1 : 1;
  }

    
}
