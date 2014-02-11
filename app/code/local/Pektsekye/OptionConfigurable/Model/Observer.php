<?php
class Pektsekye_OptionConfigurable_Model_Observer extends Mage_Core_Model_Abstract
{
  protected $_originalProduct;
  protected $_duplicatedProduct;
  protected $_prevoiusTableName = '';
  protected $_loadedProductId;
  protected $_innerProductLoad = false;  
  
  
	public function productSaveBefore(Varien_Event_Observer $observer)
	{		
		$product = $observer->getEvent()->getProduct();
		if ($product->getId() == null)
		  return;
		  
		$productId   = (int) $product->getId();		
		$storeId	   = (int) $product->getStoreId();			      
    $relation    = $product->getOptionconfigurableRelation();
    $attribute   = $product->getOptionconfigurableAttribute(); 
    $aoption     = $product->getOptionconfigurableAoption();     
    $option      = $product->getOptionconfigurableOption();
    $value       = $product->getOptionconfigurableValue();     
     
    if (!empty($relation))
      Mage::getModel('optionconfigurable/relation')->saveRelationsData($productId, Zend_Json::decode($relation));
    if (!empty($attribute))			
      Mage::getModel('optionconfigurable/attribute')->saveAttributes($productId, $storeId, $attribute);
    if (!empty($aoption))			
      Mage::getModel('optionconfigurable/aoption')->saveValues($productId, $storeId, $aoption);          
    if (!empty($option))			
      Mage::getModel('optionconfigurable/option')->saveOptions($productId, $storeId, $option);
    if (!empty($value))			
      Mage::getModel('optionconfigurable/value')->saveValues($productId, $storeId, $value);          	         	


    if ($product->isConfigurable()){    
      $data = $product->getConfigurableAttributesData();
      if ($data){
      
        $old = array();       
        foreach ($data as $attribute){
          $old[$attribute['attribute_id']] = array();
          foreach ($attribute['values'] as $value)
            $old[$attribute['attribute_id']][] = $value['value_index']; 
        } 
     
        $attributeIds = array();
        $attributeValueIds = array();        
        $attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);                
        foreach ($attributes as $attribute){
            $id = $attribute['attribute_id'];
            
            $valueIds = array();
            foreach ($attribute['values'] as $value)
              $valueIds[] = $value['value_index'];
              
            $delete = array_diff($old[$id], $valueIds);
            if (count($delete) == count($old[$id])){
              $attributeIds[] = $id;    
            } else {
              $attributeValueIds = array_merge($attributeValueIds, $delete);
            }                              
        }
                                         
        if (count($attributeIds) > 0 || count($attributeValueIds) > 0){
          Mage::getModel('optionconfigurable/attribute')->deleteAttributeValues($productId, $attributeValueIds);
          Mage::getModel('optionconfigurable/relation')->deleteAttributeValues($productId, $attributeIds, $attributeValueIds);
        }
      }
    }    
					    
	}


  public function selectNotSelected($observer)
  {
      $controller = $observer->getControllerAction();
      $request = $controller->getRequest();
 
      if ($request->getPost('super_attribute') == null){

        $productId = $request->getParam('product');
               
        if ($productId){
          $product = Mage::getModel('catalog/product')->load($productId);
	        $helper = Mage::helper('optionconfigurable');
          if ($product->getId() && $product->isConfigurable() && !isset($params['super_attribute']) && !$helper->hasVisibleRequiredOption($product)){	  
            $data = array();
            $attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);                
            foreach ($attributes as $attribute){
              foreach ($attribute['values'] as $value) {                            
                if ($value['label'] == 'not_selected'){
                  $data[$attribute['attribute_id']] = (string) $value['value_index'];
                  break;
                }
              }              
            }         
            $request->setPost('super_attribute', $data);               
          }  
        }
      }

      return $this;
  }



	public function productSaveAfter(Varien_Event_Observer $observer)
	{		
		$product = $observer->getEvent()->getProduct();
		
    $option = $product->getOptionconfigurableOption();		
    if (!empty($option)){			
      Mage::getModel('optionconfigurable/option')->saveCustomOptionsOrder($option);		
		}
		
		
		$currentProductId       = (int) $product->getId();
    $importSku              = $product->getOptionconfigurableImportSku();
    $importAttributeOptions = $product->getOptionconfigurableImportAttributeOptions(); 
    $importCustomOptions    = $product->getOptionconfigurableImportCustomOptions();    
	
    if (!empty($importSku)){
    
      $originalProduct = Mage::getModel('catalog/product');
      $originalProductId = $originalProduct->getIdBySku($importSku);            	
      if ($originalProductId == null){
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('optionconfigurable')->__('Product SKU "%s" does not exist.', $importSku));        
        return;
      } 
      
      $originalProduct->load($originalProductId);
      
      $importType = '';
      if (!empty($importAttributeOptions) && $importAttributeOptions != 'no' && $originalProduct->isConfigurable()){
        $isComplete = Mage::getModel('optionconfigurable/attribute')->importOptions($originalProduct, $currentProductId, $product->getStoreId(), $importAttributeOptions);
        if (!$isComplete)
          return;      
        $importType = 'attribute';
      }       

      if ($importCustomOptions == 1 || $importAttributeOptions == null){            	
        $isComplete = Mage::getModel('optionconfigurable/option')->importOptions($originalProduct, $currentProductId);
        if (!$isComplete)
          return;         
        $importType = $importType == '' ? 'option' : 'both';         
      }
      
      if ($importType != '')
        Mage::getModel('optionconfigurable/relation')->importRelations($originalProduct, $currentProductId, $importType);
      
    }


   
   
  } 
	
	
	
	public function productDuplicate(Varien_Event_Observer $observer)
	{	  
	  $this->_originalProduct   = $observer->getEvent()->getCurrentProduct();	  
	  $this->_duplicatedProduct = $observer->getEvent()->getNewProduct();
	  
	  //execution of this function continues in self::resourceGetTablename	 		    
	}
	
	
	
	/*
    the following function is called just after:
    app/code/core/Mage/Catalog/Model/Product.php
    function duplicate
    line 1445 $this->getOptionInstance()->duplicate
    Magento 1.7
	*/
		public function resourceGetTableName(Varien_Event_Observer $observer)
	{
    if (!isset($this->_duplicatedProduct))
      return;	

    $newProductId = $this->_duplicatedProduct->getId();	
	  $tableName = $observer->getEvent()->getTableName();	  
    if ($newProductId != null && $this->_prevoiusTableName == 'catalog_product_option' && $tableName == 'catalog_product_entity'){
      Mage::getModel('optionconfigurable/option')->copyOptionData($this->_originalProduct, $newProductId);      
      Mage::getModel('optionconfigurable/relation')->importRelations($this->_originalProduct, $newProductId, 'option');
      unset($this->_duplicatedProduct);
    }
    	 
    $this->_prevoiusTableName = $tableName;    				    
	}		


 
	/*
    The following function is used to make possible
    to add product with hidden required option to the cart
    from the front-end product page, wishlist, admin order edit page.
    And to make it not display the "The product has required options"
    error message on the cart page.
	*/ 
  public function unsetRequired($observer)
  {   	    
     if ($this->_innerProductLoad)
      return $this;
      
    $collection = $observer->getEvent()->getCollection();
    if ($collection instanceof Mage_Catalog_Model_Resource_Product_Option_Collection){

      $request = Mage::app()->getRequest();
      $path = $request->getModuleName() .'_'. $request->getControllerName() .'_'. $request->getActionName();

      if (in_array($path, array('sales_order_reorder','checkout_cart_add','checkout_cart_index','wishlist_index_cart','checkout_cart_updateItemOptions','admin_sales_order_edit_loadBlock','admin_sales_order_edit_save','admin_sales_order_edit_index'))
          || preg_match('/^(checkout_onepage|paypal_express)/', $path)){
                  
        if ($path == 'wishlist_index_cart' && $request->getPost('product') == null && isset($this->_loadedProductId)){
  
          $helper = Mage::helper('optionconfigurable');          
          $productModel = Mage::getModel('catalog/product');
          
          $this->_innerProductLoad = true;            
          $product = $productModel->load($this->_loadedProductId);         
          if ($helper->hasVisibleRequiredOption($product)){  
            return $this;
          }
        }
          
        foreach ($collection as $option) {
          if ($option->getIsRequire()){
            $option->setIsRequire(false);
          }        
        }
      }
    }        
  }
  


  public function productLoadAfter($observer)
  { 
    $this->_loadedProductId = $observer->getEvent()->getProduct()->getId();
    $this->unsetRequiredOptions($observer);    
  } 
   
   
   
	/*
    The following function is used to remove the "* Required Fields"
    text on the front-end product page ? ...
	*/   
  public function unsetRequiredOptions($observer)
  {
    $request = Mage::app()->getRequest();
    $path = $request->getModuleName() .'_'. $request->getControllerName() .'_'. $request->getActionName(); 
    
    if ($path == 'catalog_product_view'){
      $product = $observer->getEvent()->getProduct();
      if ($product->getRequiredOptions() == 1 && $product->isConfigurable()){ 
        $product->setRequiredOptions(0);
        $product->setOcRequiredOptions(true);        
      }
    }  
  }
    
    
	/*
    The following function is used to make possible
    to add product with hidden required option to the cart
    from the front-end category product list page.
	*/ 
  public function unsetRequiredOptionsToCollection($observer)
  {  
    $request = Mage::app()->getRequest();
    $path = $request->getModuleName() .'_'. $request->getControllerName() .'_'. $request->getActionName();     
    if ($path == 'catalog_category_view'){  

      $productModel = Mage::getModel('catalog/product');
      $helper = Mage::helper('optionconfigurable');
      $collection = $observer->getEvent()->getCollection();
      foreach ($collection as $item){
        if ($item->getRequiredOptions() == 1 && !$item->isGrouped() && $item->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE){
          if (!$helper->hasVisibleRequiredOption($productModel->load($item->getId()))){
            $item->setRequiredOptions(0);
          }
          $productModel->reset();                  
        } 				  
      }
    }
  }	  	
}

