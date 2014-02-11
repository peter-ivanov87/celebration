<?php

class Pektsekye_OptionConfigurable_Block_Order_Item_Renderer_Default extends Mage_Sales_Block_Order_Item_Renderer_Default
{
//to apply sort order on the front-end customer account order page
    public function getItemOptions()
    { 
      $options = parent::getItemOptions();
      
      $options = Mage::helper('optionconfigurable')->applySortOrder($this->getOrderItem()->getProductId(), $options);

      return $options;
    }


}
