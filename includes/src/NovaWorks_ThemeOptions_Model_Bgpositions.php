<?php class NovaWorks_ThemeOptions_Model_Bgpositions
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'left top', 'label'=>Mage::helper('themeoptions')->__('Left Top')),
            array('value'=>'left center', 'label'=>Mage::helper('themeoptions')->__('Left Center')),
            array('value'=>'left bottom', 'label'=>Mage::helper('themeoptions')->__('Left Bottom')), 
            array('value'=>'center top', 'label'=>Mage::helper('themeoptions')->__('Center Top')),
            array('value'=>'center center', 'label'=>Mage::helper('themeoptions')->__('Center Center')),
            array('value'=>'center bottom', 'label'=>Mage::helper('themeoptions')->__('Center Bottom')),      
            array('value'=>'right top', 'label'=>Mage::helper('themeoptions')->__('Right Top')),
            array('value'=>'right center', 'label'=>Mage::helper('themeoptions')->__('Right Center')),
            array('value'=>'right bottom', 'label'=>Mage::helper('themeoptions')->__('Right Bottom'))          
        );
    }

}?>