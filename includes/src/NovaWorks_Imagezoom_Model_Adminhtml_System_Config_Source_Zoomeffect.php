<?php
class NovaWorks_Imagezoom_Model_Adminhtml_System_Config_Source_Zoomeffect
{
	public function toOptionArray()
	{
		return array(
			array('value' => "0", 	'label' => Mage::helper('imagezoom')->__('None')),
			array('value' => "1", 	'label' => Mage::helper('imagezoom')->__('Tint')),
			array('value' => "2", 	'label' => Mage::helper('imagezoom')->__('Soft Focus')),
		);
	}
}
?>