<?php
class Pektsekye_OptionConfigurable_Model_Convert_Parser_Relation extends Mage_Dataflow_Model_Convert_Parser_Csv
{

   public function unparse()
  {
    $io = $this->getBatchModel()->getIoAdapter();
    $io->open();
  
    $optionIds = Mage::getModel('optionconfigurable/relation')->getUsedOptionIds();      
    $io->write($this->getCsvString(array('option_ids', Zend_Json::encode($optionIds))));
    
    $productSkus = Mage::getResourceModel('optionconfigurable/relation')->getUsedPoductSkus();      
    foreach($productSkus as $productId => $productSku){                
      $data = array( 
        'attributes' => Mage::getModel('optionconfigurable/attribute')->getAttributesAllStores($productId),
        'aoptions'   => Mage::getModel('optionconfigurable/aoption')->getAoptionsAllStores($productId),          
        'options'    => Mage::getModel('optionconfigurable/option')->getOptionsAllStores($productId),  
        'values'     => Mage::getModel('optionconfigurable/value')->getValuesAllStores($productId),                           
        'relations'  => Mage::getModel('optionconfigurable/relation')->getRelations($productId) 
      );             
      $io->write($this->getCsvString(array($productSku, Zend_Json::encode($data))));	      
    }

    $io->close();
    
    return $this;
  }

}
