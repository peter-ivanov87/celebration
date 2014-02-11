<?php

class Pektsekye_OptionConfigurable_Block_Order_Email_Items_Order_Default extends Mage_Sales_Block_Order_Email_Items_Order_Default
{
//to apply sort order on the front-end order complete customer's email    
    public function getItemOptions()
    {
      $options = parent::getItemOptions();
      
      $options = Mage::helper('optionconfigurable')->applySortOrder($this->getItem()->getProductId(), $options);

      return $options;
    }

}
