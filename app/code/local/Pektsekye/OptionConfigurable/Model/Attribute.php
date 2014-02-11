<?php

class Pektsekye_OptionConfigurable_Model_Attribute extends Mage_Core_Model_Abstract
{	
    
    public function _construct()
    {
      parent::_construct();
      $this->_init('optionconfigurable/attribute');
    }


    public function getAttributes($productId, $storeId = 0)
    {        
      return $this->getResource()->getAttributes($productId, $storeId);                            
    } 



    public function getAttributesAllStores($productId)
    {   
    
      $data = $this->getResource()->getAttributes($productId, 0);
      foreach($data as $k => $r){
        $data[$k]['notes'] = $this->getResource()->getStoreNotes($productId, $r['attribute_id']);         
      }
      return $data;                          
    } 


    public function importOptions($originalProduct, $currentProductId, $storeId, $action)
    {          
      $originalUsedIds = $originalProduct->getTypeInstance(true)->getUsedProductIds($originalProduct);     
      if (count($originalUsedIds) == 0)
        return false;
        
      $currentProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($currentProductId);  
      $currentUsedIds = $currentProduct->getTypeInstance(true)->getUsedProductIds($currentProduct);     
      if (count($currentUsedIds) > 0)
        return false;
  
      if ($originalProduct->getAttributeSetId() != $currentProduct->getAttributeSetId()){ 
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('optionconfigurable')->__('Product Attribute Set does not match.'));      
        return false;        
      }
      
      $originalUsedAttributeIds = $originalProduct->getTypeInstance(true)->getUsedProductAttributeIds($originalProduct);
      $currentUsedAttributeIds  = $currentProduct->getTypeInstance(true)->getUsedProductAttributeIds($currentProduct);
      sort($originalUsedAttributeIds);
      sort($currentUsedAttributeIds);    
      if ($originalUsedAttributeIds !== $currentUsedAttributeIds){
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('optionconfigurable')->__('Product Attributes used for Product Association do not match.'));      
        return false;                
      }  
 

     $superAttributeId = array();
     $currentAttributes = $originalProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($currentProduct);
     foreach ($currentAttributes as $attribute){
       $superAttributeId[$attribute['attribute_id']] = $attribute['id'];
     } 
     
     $pricing = array();             
     $originalAttributes = $originalProduct->getTypeInstance(true)->getConfigurableAttributesAsArray($originalProduct);       
     foreach ($originalAttributes as $attribute) {
          Mage::getModel('catalog/product_type_configurable_attribute')
             ->setData($attribute)
             ->setId($superAttributeId[$attribute['attribute_id']])
             ->setStoreId($storeId)
             ->setProductId($currentProductId)
             ->save();
             
          foreach ($attribute['values'] as $value) {            
            $pricing[$attribute['attribute_code']][$value['value_index']] = array('value' => $value['pricing_value'], 'is_percent' => $value['is_percent']);
          }  
      }
      
      $selectIds = array(); 
      if ($action == 'create'){             
        foreach($originalUsedIds as $productId)     
          $selectIds[] = $this->createProduct($currentProduct, $productId, $pricing);                                      
      } else {     
        $selectIds = $originalUsedIds;      
      }            
       
      Mage::getResourceModel('catalog/product_type_configurable')->saveProducts($currentProduct, $selectIds);

      $this->getResource()->copyAttributes($originalProduct->getId(), $currentProductId);
      
      Mage::getResourceModel('optionconfigurable/aoption')->copyAoptions($originalProduct->getId(), $currentProductId);
              
      return true;
    }   
   
   
   
    public function createProduct($configurableProduct, $originalSimpleProductId, $pricing)
    {
      $result = array();

      $originalSimpleProduct = Mage::getModel('catalog/product')
          ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
          ->load($originalSimpleProductId);

      $product = Mage::getModel('catalog/product')
          ->setStoreId(0)
          ->setTypeId(Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
          ->setAttributeSetId($configurableProduct->getAttributeSetId());


      foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
          if ($attribute->getIsUnique()
              || $attribute->getAttributeCode() == 'url_key'
              || $attribute->getFrontend()->getInputType() == 'gallery'
              || $attribute->getFrontend()->getInputType() == 'media_image'
              || !$attribute->getIsVisible()) {
              continue;
          }

          $product->setData(
              $attribute->getAttributeCode(),
              $originalSimpleProduct->getData($attribute->getAttributeCode())
          );
      }
      
      
      $product->setPrice($configurableProduct->getPrice());
      $product->setWeight($originalSimpleProduct->getWeight());
      $product->setStatus($originalSimpleProduct->getStatus());
      $product->setVisibility($originalSimpleProduct->getVisibility());
      
      
      $stockData = array();
      $stockItem = $originalSimpleProduct->getStockItem();                  
      $stockDataKeys = array(
        'manage_stock',
        'use_config_manage_stock',                    
        'qty',
        'min_qty',
        'use_config_min_qty',
        'min_sale_qty',
        'use_config_min_sale_qty',            
        'max_sale_qty', 
        'use_config_max_sale_qty',
        'is_qty_decimal',       
        'is_decimal_divided',            
        'backorders',
        'use_config_backorders',                                                                      
        'notify_stock_qty', 
        'use_config_notify_stock_qty', 
        'enable_qty_increments', 
        'use_config_enable_qty_inc',
        'qty_increments', 
        'use_config_qty_increments',                                     
        'is_in_stock'                                               
      );                       
      foreach($stockDataKeys as $key){
        $stockData[$key] = $stockItem->getData($key);        
      }
      $product->setStockData($stockData);
        
                           
      $product->setWebsiteIds($configurableProduct->getWebsiteIds());
  
      
      $prices = array();
      $autogenerateOptions = array();
      foreach ($configurableProduct->getTypeInstance()->getConfigurableAttributes() as $attribute) {
          $attributeCode = $attribute->getProductAttribute()->getAttributeCode();
          $valueId = $originalSimpleProduct->getData($attributeCode);
          $prices[] = $pricing[$attributeCode][$valueId];
          $autogenerateOptions[] = $product->getAttributeText($attributeCode);            
      }
      
      $name = $configurableProduct->getName() . '-' . implode('-', $autogenerateOptions);
      $product->setName($name);

      $sku = $configurableProduct->getSku() . '-' . implode('-', $autogenerateOptions);
      $productId = Mage::getResourceModel('catalog/product')->getIdBySku($sku);
      if ($productId != null){
        return $productId;
      }          
      $product->setSku($sku);
      
      
      $additionalPrice = 0;
      foreach ($prices as $price) {
        if (empty($price['value']))
           continue;                
        if (!empty($price['is_percent']))
           $price['value'] = ($price['value']/100)*$product->getPrice();                
        $additionalPrice += $price['value'];
      }      
      $product->setPrice($product->getPrice() + $additionalPrice);
      
      $product->getOptionInstance()->unsetOptions(); // do not save custom options of configurable product to simple products because of catalog/product_option singleton in Mage_Catalog_Model_Product getOptionInstance()      
      
      $product->save();
      
      return $product->getId();
    }   
   
   
    
    public function saveAttributes($productId, $storeId, $attributes)
    {
      return $this->getResource()->saveAttributes($productId, $storeId, $attributes);
    }
    
    
    
    public function deleteAttributeValues($productId, $attributeValueIds)
    {
      $attributes = $this->getAttributes($productId, 0);
      foreach($attributes as $id => $value){
        if (in_array($value['default'], $attributeValueIds))
          $attributes[$id]['default'] = '';
      }
      $this->saveAttributes($productId, 0, $attributes);
    }   
   
    
    
    public function saveCsvAttributes($productId, $attributes, $tIds, $storeIds)
    {
      $attributes = $this->translateCsvAttributes($attributes, $tIds, $storeIds);      
      $this->saveAttributes($productId, 0, $attributes);
    }
    
    
    
    public function translateCsvAttributes($attributes, $tIds, $storeIds)
    {
      $t = 'a';
      $tAttributes = array();       
      foreach($attributes as $id => $value){
        if (isset($tIds[0][$t][$id])){
          $tId = $tIds[0][$t][$id];
          $tValue = $value;
          if (isset($value['required']) && $value['required'] == 1){
            $tValue['required'] = 1;
          }          
          if (isset($value['default'])){
            $vId = $value['default'];
            if (isset($tIds[1][$t][$vId])) 
              $tValue['default'] = $tIds[1][$t][$vId];                                 
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
          $tAttributes[$tId] = $tValue;
        }   
      } 
                
      return $tAttributes;
    }
    
    
}
