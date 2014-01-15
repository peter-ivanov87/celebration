<?php class NovaWorks_ThemeOptions_Model_Animationtype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fade', 'label'=>Mage::helper('themeoptions')->__('Fade')),
            array('value'=>'slide', 'label'=>Mage::helper('themeoptions')->__('Slide'))
        );
    }

}?>