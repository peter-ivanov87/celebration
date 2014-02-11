<?php
class Pektsekye_OptionConfigurable_Block_Product_View_Js extends  Mage_Catalog_Block_Product_View_Options
{ 

    protected $_options;
    protected $_inPreconfigured;
    protected $_attributes;
    protected $thumbnailDirUrl = '';		
    protected $pickerImageDirUrl = '';      
    
    public function getDataJson()
    {                 
      $data = array(
        'sortedOIds'         => array(),      
        'isRequired'         => array('a' => array(), 'o' => array()),
        'checkedIds'         => array('a' => array(), 'o' => array()),
        'notDepVIdsByOId'    => array('a' => array(), 'o' => array()),
        'vIdsByOId'          => array('a' => array(), 'o' => array()),
        'hasNotSelected'     => array(),
        'attributesAffected' => false
      );     	

      $rData = Mage::getModel('optionconfigurable/relation')->getRelationData($this->getProduct());      

      $outOfStockVIds = array();
      
      $position = 1;      
      foreach($this->getOptions() as $option){
        $t = $option['type'];
        $id = $option['id'];

        if ($t == 'a'){        
          if (isset($option['hasNotSelected']))        
            $data['hasNotSelected'][$id] = 1;        
        
          if ($option['hasDefault'] || $option['original_position'] != $position)
            $data['attributesAffected'] = true;                    
          $position++;
          $outOfStockVIds = array_merge($outOfStockVIds, $option['out_of_stock_value_ids']);           
        }
 
        if ($option['required'] == 1)
          $data['isRequired'][$t][$id] = 1;                  
        
        if (isset($option['checkedIds']))
          $data['checkedIds'][$t][$id] = $option['checkedIds'];

        $data['layout'][$t][$id] = $option['layout'];
        $data['note'][$t][$id]   = $option['note'];
        $data['popup'][$t][$id]  = (bool) $option['popup'] == 1;
          
        $firstValueId = null;     
        foreach($option['values'] as $value){
        
          $this->prepareImages($value['image']);
          	
          $valueId = $value['id'];
                  
          $hasParent = isset($rData['pVIdsByVId'][$t][$valueId]) || isset($rData['pVIdsByOId'][$t][$id]);
          $hasChildren = isset($rData['cVIdsByVId'][$t][$valueId]) || isset($rData['cOIdsByVId'][$t][$valueId]);            

          if (!$hasParent)
            $data['notDepVIdsByOId'][$t][$id][] = $valueId;
   
          $data['image'][$t][$valueId] = $value['image'];
          $data['description'][$t][$valueId] = $value['description'];   
           
          if ($t == 'a' && ($hasParent || $hasChildren))
            $data['attributesAffected'] = true;
            
          if ($firstValueId == null)  
            $firstValueId = $valueId;                                                 
        }
        
        $data['vIdsByOId'][$t][$id] = $option['value_ids'];   
        $data['sortedOIds'][] = array('type' => $t, 'id' => $id, 'optionType' => $option['option_type']);                
      }

      $keys = array('optionIds','oIdByVId','cOIdsByVId','cOIdsByOId','cVIdsByVId'); 
      foreach($keys as $key)
        $data[$key] = $rData[$key];
  
      foreach($data['cVIdsByVId'] as $t => $a){
        foreach($a as $vid => $aa){      
          if (isset($aa['a'])){
            $inStockVIds = array_diff($data['cVIdsByVId'][$t][$vid]['a'], $outOfStockVIds);
            $data['cVIdsByVId'][$t][$vid]['a'] = array_values($inStockVIds);
          }
        }        
      }
  
      return Zend_Json::encode($data);                              
    }



    public function getOptions()
    {
      if (!isset($this->_options)){

        $options = array();
        
        $hasOrder = false;
        $filter = Mage::getModel('core/email_template_filter');  
                
        $options = array();    
                                                         
        if ($this->getProduct()->isConfigurable()){
                
          $position = 1;      
          $ocAttributes = Mage::getModel('optionconfigurable/attribute')->getAttributes((int)$this->getProduct()->getId(), (int)$this->getProduct()->getStoreId());			                  
          foreach ($this->_getAttributes() as $attribute) {
            $id = (int) $attribute['attribute_id'];           
            
            $sortOrder = isset($ocAttributes[$id]['order']) ? (int) $ocAttributes[$id]['order'] : 0;   
            $note = isset($ocAttributes[$id]['note']) ? $ocAttributes[$id]['note'] : '';
            $layout = isset($ocAttributes[$id]['layout']) ? $ocAttributes[$id]['layout'] : '';
            $popup = isset($ocAttributes[$id]['popup']) ? (int) $ocAttributes[$id]['popup'] : 0;              
                    
            $option = array(
              'type' => 'a',
              'id' => $id,
              'option_type' => 'attribute_select', // correct it 
              'sort_order' => $sortOrder,
              'required' => isset($ocAttributes[$id]['required']) ? (int) $ocAttributes[$id]['required'] : 0,
              'hasDefault' => isset($ocAttributes[$id]['default']) && $ocAttributes[$id]['default'] != '',            
              'original_position' => $position,
              'out_of_stock_value_ids' => array(),
              'value_ids' => array(),              
              'note' => $filter->filter($note),   
              'layout' => $this->checkLayout('drop_down', $layout),  
              'popup' => $popup,                                                                                                                                 
              'values' => array()
            );
  
            if ($this->_getInPreconfigured()){          
              $configValue = $this->getProduct()->getPreconfiguredValues()->getData('super_attribute/' . $id);
              if (!is_null($configValue)) 
                $option['checkedIds'][] = (int) $configValue;		                        
            } elseif (isset($ocAttributes[$id]['default']) && $ocAttributes[$id]['default'] != '') {
              $option['checkedIds'][] = (int) $ocAttributes[$id]['default'];           
            }
            
            $ocValues = Mage::getModel('optionconfigurable/aoption')->getValues($this->getProduct()->getId(), (int)$this->getProduct()->getStoreId());                        
            foreach ($attribute['values'] as $value){
              $valueId = (int) $value['value_index'];

              $image = isset($ocValues[$valueId]['image']) ? $ocValues[$valueId]['image'] : '';
              $description = isset($ocValues[$valueId]['description']) ? $ocValues[$valueId]['description'] : '';
                                                                 
              $option['values'][] = array(
                'id' => $valueId,
                'image' => $image,
                'description' => $filter->filter($description)              
              );
                            
              if (!$this->ckeckInStock($id, $valueId)){
                $option['out_of_stock_value_ids'][] = $valueId;              
                continue;
              }
                
              if ($value['label'] == 'not_selected') 
                $option['hasNotSelected'] = 1;
              $option['value_ids'][] = $valueId;                                                                              
            }
            
           $options[] = $option;
           $hasOrder |= $sortOrder > 0; 
           $position++;                           
          }        
        }
        
        $ocOptions = Mage::getModel('optionconfigurable/option')->getOptions((int)$this->getProduct()->getId(), (int)$this->getProduct()->getStoreId());
        foreach ($this->getProduct()->getOptions() as $_option) {
            $id = (int) $_option->getOptionId();
 
            $note = isset($ocOptions[$id]['note']) ? $ocOptions[$id]['note'] : '';            
            $layout = isset($ocOptions[$id]['layout']) ? $ocOptions[$id]['layout'] : '';
            $popup = isset($ocOptions[$id]['popup']) ? (int) $ocOptions[$id]['popup'] : 0; 
            
            $option = array(
                'type' => 'o',
                'id' => $id,
                'option_type' => $_option->getType(),              
                'sort_order' => $_option->getSortOrder(),
                'required' => $_option->getIsRequire() ? 1 : 0,                                                                             
                'note' => $filter->filter($note),                  
                'layout' => $this->checkLayout($_option->getType(), $layout),  
                'popup' => $popup,                                                                                                                                   
                'values' => array(),
                'value_ids' => array()                
            ); 
                         
            if ($_option->getGroupByType($_option->getType()) == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT){
            
              if ($this->_getInPreconfigured()){                
                $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $id);	
                if (!is_null($configValue)){
                  if (is_array($configValue)){
                    foreach($configValue as $valueId)
                      $option['checkedIds'][] = (int) $valueId;								
                  } else {
                    $option['checkedIds'][] = (int) $configValue;							
                  }
                }
              } elseif (isset($ocOptions[$id]['default']) && $ocOptions[$id]['default'] != ''){
                foreach(explode(',', $ocOptions[$id]['default']) as $valueId)
                  $option['checkedIds'][] = (int) $valueId;                
              }
              
              $ocValues = Mage::getModel('optionconfigurable/value')->getValues($this->getProduct()->getId(), (int)$this->getProduct()->getStoreId());       
              foreach ($_option->getValues() as $value){ 
                $valueId = (int) $value->getOptionTypeId(); 
                
                $image = isset($ocValues[$valueId]['image']) ? $ocValues[$valueId]['image'] : '';
                $description = isset($ocValues[$valueId]['description']) ? $ocValues[$valueId]['description'] : '';              
                                                                           
                $option['values'][] = array(
                  'id' => $valueId,
                  'image' => $image,
                  'description' => $filter->filter($description)                              
                ); 
                $option['value_ids'][] = $valueId;                
              }
            }
            
            $options[] = $option;
         }                
               
         if ($hasOrder){                     
            usort($options, array(Mage::helper('optionconfigurable'), "sortOptions"));
         }
          $this->_options = $options;
       }
                     
       return $this->_options;                         
    }   
     
     
     public function ckeckInStock($id, $valueId)
    {   
      $inStockOptions = $this->getInStockOptions();
      return isset($inStockOptions[$id][$valueId]);
    }
    
    
     public function getInStockOptions()
    {
      if (!$this->hasInStockOptions()) {    
        $inStockOptions    = array();
        foreach ($this->getAllowProducts() as $product) {
            $productId  = $product->getId();

            foreach ($this->_getAttributes() as $attribute) {
                $productAttributeId = $attribute['attribute_id'];
                $attributeValue     = $product->getData($attribute['attribute_code']);
                if (!isset($inStockOptions[$productAttributeId])) {
                    $inStockOptions[$productAttributeId] = array();
                }

                if (!isset($inStockOptions[$productAttributeId][$attributeValue])) {
                    $inStockOptions[$productAttributeId][$attributeValue] = array();
                }
                $inStockOptions[$productAttributeId][$attributeValue][] = $productId;
            }
        }
        $this->setInStockOptions($inStockOptions);
      } 
      return $this->getData('in_stock_options');         
    }
    
      
    public function getAllowProducts()
    {
      if (!$this->hasAllowProducts()) {
          $products = array();
          $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
          $allProducts = $this->getProduct()->getTypeInstance(true)
              ->getUsedProducts(null, $this->getProduct());
          foreach ($allProducts as $product) {
              if ($product->isSaleable() || $skipSaleableCheck) {
                  $products[] = $product;
              }
          }
          $this->setAllowProducts($products);
      }
      return $this->getData('allow_products');
    }
    
        
    
    public function _getInPreconfigured()
    { 
      if (!isset($this->_inPreconfigured)){
      			
        if (!$this->getProduct()->hasPreconfiguredValues())
          return $this->_inPreconfigured = false;
  
        if ($this->getProduct()->isConfigurable()){			                  
          foreach ($this->_getAttributes() as $attribute) {
            $configValue = $this->getProduct()->getPreconfiguredValues()->getData('super_attribute/' . $attribute['attribute_id']);
            if (!is_null($configValue)) 
              return $this->_inPreconfigured = true;                        
          }        
        }
          
        foreach ($this->getProduct()->getOptions() as $option) { 
          $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getOptionId());	
          if (!is_null($configValue))
            return $this->_inPreconfigured = true;            
        }			
        
        $this->_inPreconfigured = false;
			}
			
			return $this->_inPreconfigured;
	 	}	


    public function _getAttributes()
    { 			
      if (!isset($this->_attributes))
        $this->_attributes = $this->getProduct()->getTypeInstance(true)->getConfigurableAttributesAsArray($this->getProduct());
						
			return $this->_attributes;
	 	}
	 	


    public function prepareImages($image)
    { 	
      if (!empty($image)){
        $thumbnailUrl = $this->makeThumbnail($image);			
        $pickerImageUrl = $this->makePickerImage($image);
        if ($this->thumbnailDirUrl == ''){
          $this->thumbnailDirUrl = str_replace($image, '', $thumbnailUrl);					
          $this->pickerImageDirUrl = str_replace($image, '', $pickerImageUrl);								
        }	
      }
	  }
		
		
    public function makeThumbnail($image)
    { 	
		  $thumbnailUrl = $this->helper('catalog/image')
        ->init($this->getProduct(), 'thumbnail', $image)
        ->keepFrame(true)
  // Uncomment the following line to set Thumbnail RGB Background Color:
  //			->backgroundColor(array(246,246,246))	
  
  // Set Thumbnail Size:			
        ->resize(100,100)
        ->__toString();
			
		  return $thumbnailUrl;
	  }		
		
		
    public function makePickerImage($image)
    { 	
			$pickerImageUrl = $this->helper('catalog/image')
				->init($this->getProduct(), 'thumbnail', $image)
				->keepFrame(false)
				->resize(30,30)
				->__toString();			
			return $pickerImageUrl;
		}	
		
    public function getThumbnailDirUrl()
    { 			
			return $this->thumbnailDirUrl;
	 	}	
	
	
    public function getPickerImageDirUrl()
    { 			
			return $this->pickerImageDirUrl;
	 	}

    public function getPlaceholderUrl()
    {
			return Mage::getDesign()->getSkinUrl($this->helper('catalog/image')->init($this->getProduct(), 'small_image')->getPlaceholder());
	 	}	
	
	
    public function getProductBaseMediaUrl()
    { 			
			return Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrl();
	 	}	
	
	 	
	 	
    public function checkLayout($optionType, $layout)
    { 		 	
      $layouts = array(
        Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO => array(
            'above' =>1,        
            'before'=>1,
            'below' =>1,
            'swap'  =>1,
            'grid'  =>1,    
            'list'  =>1               
          ),        
        Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX => array(
            'above'=>1,         
            'below'=>1,
            'grid' =>1,   
            'list' =>1    
          ),        
        Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN => array(
            'above'     =>1,         
            'before'    =>1,
            'below'     =>1,
            'swap'      =>1,
            'picker'    =>1, 
            'pickerswap'=>1                 
          ),
        Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE => array(
            'above'=>1,        
            'below'=>1         
          )           
      );	 	
        
      return isset($layouts[$optionType][$layout]) ? $layout : 'above';
    }	
    
    
    	 	
    public function hasRequiredOptions()
    { 			
			return $this->getProduct()->getOcRequiredOptions() ? 'true' : 'false';
	 	}	
	 	
    public function hasCustomOptions()
    { 			
			return count($this->getProduct()->getOptions()) > 0 ? 'true' : 'false';
	 	}		 	
	 	
    public function getInPreconfigured()
    { 			
			return $this->_getInPreconfigured() ? 'true' : 'false';
	 	}		

    public function isConfigurable()
    {
      return $this->getProduct()->isConfigurable() ? 'true' : 'false';
    }
	 	
	 	
    protected function _toHtml()
    {
      if ($this->getProduct()->isSaleable() && count($this->getOptions()) > 0) {
        return parent::_toHtml();
      }
      return '';
    }	 		
}
