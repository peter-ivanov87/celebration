<?php class NovaWorks_ThemeOptions_Model_Bgrepeat
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'no-repeat', 'label'=>Mage::helper('themeoptions')->__('No Repeat')),
            array('value'=>'repeat', 'label'=>Mage::helper('themeoptions')->__('Repeat')),
            array('value'=>'repeat-x', 'label'=>Mage::helper('themeoptions')->__('Repeat X')), 
            array('value'=>'repeat-y', 'label'=>Mage::helper('themeoptions')->__('Repeat Y'))       
        );
    }

}?>