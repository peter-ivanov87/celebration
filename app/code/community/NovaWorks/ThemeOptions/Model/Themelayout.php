<?php class NovaWorks_ThemeOptions_Model_Themelayout
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('Wide Layout')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('Boxed Layout'))            
        );
    }

}?>