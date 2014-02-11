<?php

class Pektsekye_OptionConfigurable_Model_Product_Option_Type_Select extends Mage_Catalog_Model_Product_Option_Type_Select
{

    public function isCustomizedView()
    {
      return $this->canDisplayOptionImages();         
    }

    /**
     * Return option html
     *
     * @param array $optionInfo
     * @return string
     */
    public function getCustomizedView($optionInfo)
    {		 
        return $optionInfo['value'];
    }	

	

    public function getFormattedOptionValue($optionValue)
    {

			$formattedValue = parent::getFormattedOptionValue($optionValue);
 			
      if ($this->canDisplayOptionImages()){
			  $option = $this->getOption();	
			  if (!$this->_isSingleSelection()) {
				  $formattedValues = explode(', ', $formattedValue);
			    $result = '';				  
				  foreach (explode(',', $optionValue) as $k => $valueId) {
				  
				    $result .= ($k > 0 ? ', ' : '') . $formattedValues[$k];
				    if (!is_null($option->getValueById($valueId))){
				    	$image = $option->getValueById($valueId)->getImage();
				    	if (is_null($image))
    				  	$image = Mage::getModel('optionconfigurable/value')->load($valueId, 'value_id')->getImage();
					    if ($image != '')				
						    $result .= $this->makeImage($image);
						}
				  }
				  $formattedValue = $result;
			  } else {
				  if (!is_null($option->getValueById($optionValue))){			  
			      $image = $option->getValueById($optionValue)->getImage();
			    	if (is_null($image))
				    	$image = Mage::getModel('optionconfigurable/value')->load($optionValue, 'value_id')->getImage();			    
				    if ($image != '')
					    $formattedValue .= $this->makeImage($image);
					}
			  }		  		  
      }
           
      return $formattedValue;
      			
   }

    public function makeImage($image)
    {    						
			$url = Mage::helper('catalog/image')->init(Mage::getModel('catalog/product'), 'thumbnail', $image)->resize(45,45)->__toString();
			return  '<img src="'.$url.'" style="vertical-align:middle;margin:5px;">';
    }
    

     public function canDisplayOptionImages()
    {	
      $request = Mage::app()->getRequest();
      $path = $request->getModuleName() .'_'. $request->getControllerName() .'_'. $request->getActionName(); 
         	 
      return Mage::getStoreConfig('checkout/cart/custom_option_images') == 1 && $path == 'checkout_cart_index';
    }
}
