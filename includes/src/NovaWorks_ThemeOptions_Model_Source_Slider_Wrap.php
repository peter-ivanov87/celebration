<?php
class NovaWorks_ThemeOptions_Model_Source_Slider_Wrap
{
    public function toOptionArray()
    {
        return array(
        	array('value' => 'null',			'label' => Mage::helper('themeoptions')->__('Disalbe')),
					array('value' => 'first',			'label' => Mage::helper('themeoptions')->__('First')),
					array('value' => 'last',			'label' => Mage::helper('themeoptions')->__('Last')),
					array('value' => 'both',			'label' => Mage::helper('themeoptions')->__('Both')),
					array('value' => 'circular',	'label' => Mage::helper('themeoptions')->__('Circular'))
        );
    }
}