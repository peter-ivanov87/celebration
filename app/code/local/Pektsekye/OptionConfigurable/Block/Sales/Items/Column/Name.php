<?php

class Pektsekye_OptionConfigurable_Block_Sales_Items_Column_Name extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{
//to apply sort order on the back-end order view page
    public function getOrderOptions()
    {
      $options = parent::getOrderOptions();
      
      $options = Mage::helper('optionconfigurable')->applySortOrder($this->getItem()->getProductId(), $options);

      return $options;      
     } 
}
