<?php
class NovaWorks_Imagezoom_Model_Adminhtml_System_Config_Source_Position
{
	public function toOptionArray()
	{
		return array(
			array('value' => "'left'", 		'label' => Mage::helper('imagezoom')->__('Left')),
			array('value' => "'right'", 	'label' => Mage::helper('imagezoom')->__('Right')),
			array('value' => "'top'", 		'label' => Mage::helper('imagezoom')->__('Top')),
			array('value' => "'bottom'", 	'label' => Mage::helper('imagezoom')->__('Bottom')),
			array('value' => "'inside'", 	'label' => Mage::helper('imagezoom')->__('Inside (Inner Zoom)'))
		);
	}
}
?>