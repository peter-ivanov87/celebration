<?php class NovaWorks_ThemeOptions_Model_Shoppingoptions
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('themeoptions')->__('Collapsed')),
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Expanded')),         
        );
    }

}?>