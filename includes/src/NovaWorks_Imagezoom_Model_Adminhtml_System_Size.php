<?php
class NovaWorks_Imagezoom_Model_Adminhtml_System_Size extends Mage_Core_Model_Config_Data
{
    public function save()
    {
		$value = $this->getValue(); //get the value from our config
		
        if($value == '')
        {
			$this->setValue(265); //set default value
        }
       
		return parent::save();
    }
}
?>