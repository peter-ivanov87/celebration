<?php

class Pektsekye_OptionConfigurable_Helper_Product_Configuration extends Mage_Catalog_Helper_Product_Configuration
{
//to apply sort order only for configurable product type on the front-end shopping cart page, onepage checkout page and wishlist page
    public function getConfigurableOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {    
      $product = $item->getProduct();
      $typeId = $product->getTypeId();
      if ($typeId != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
           Mage::throwException($this->__('Wrong product type to extract configurable options.'));
      }
      
      if ($this->canDisplayOptionImages()){
        $attributes = $this->getSelectedAttributesInfo($product);
      } else {
        $attributes = $product->getTypeInstance(true)->getSelectedAttributesInfo($product);
      }
       
      $options = array_merge($attributes, parent::getCustomOptions($item));
      
      $options = Mage::helper('optionconfigurable')->applySortOrder($item->getProductId(), $options);

      return $options;
    }


    /**
     * Retrieve Selected Attributes info
     *
     * @param  Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSelectedAttributesInfo($product)
    {

        $ocValues = Mage::getModel('optionconfigurable/aoption')->getValues($product->getId(), (int)$product->getStoreId());    

        $typeInstance = $product->getTypeInstance(true);
        
        $attributes = array();
        if ($attributesOption = $typeInstance->getProduct($product)->getCustomOption('attributes')) {
            $data = unserialize($attributesOption->getValue());
            $typeInstance->getUsedProductAttributeIds($product);

            $usedAttributes = $typeInstance->getProduct($product)->getData('_cache_instance_used_attributes');

            foreach ($data as $attributeId => $attributeValue) {
                if (isset($usedAttributes[$attributeId])) {
                    $attribute = $usedAttributes[$attributeId];
                    $label = $attribute->getLabel();
                    $value = $attribute->getProductAttribute();
                    if ($value->getSourceModel()) {
                        $value = $value->getSource()->getOptionText($attributeValue);
                        $image = isset($ocValues[$attributeValue]['image']) ? $ocValues[$attributeValue]['image'] : '';
                        if (!empty($image))		
                          $value .= $this->makeImage($image);                                                      
                    }
                    else {
                        $value = '';
                    }

                    $attributes[] = array('label'=>$label, 'value'=>$value, 'attribute_id'=>$attributeId, 'option_id'=>1, 'custom_view'=>1);
                }
            }
        }

        return $attributes;
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
