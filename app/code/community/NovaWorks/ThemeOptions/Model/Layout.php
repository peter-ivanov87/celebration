<?php class TNovaWorks_ThemeOptions_Model_Layout
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'left', 'label'=>Mage::helper('themeoptions')->__('Left')),
            array('value'=>'right', 'label'=>Mage::helper('themeoptions')->__('Right'))          
        );
    }

}?>