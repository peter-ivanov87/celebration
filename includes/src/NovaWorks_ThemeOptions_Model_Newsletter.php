<?php class NovaWorks_ThemeOptions_Model_Newsletter
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Footer')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('Sidebar'))            
        );
    }

}?>