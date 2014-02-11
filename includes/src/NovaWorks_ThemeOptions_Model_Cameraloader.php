<?php class NovaWorks_ThemeOptions_Model_Cameraloader
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'none', 'label'=>Mage::helper('themeoptions')->__('None')),
            array('value'=>'pie', 'label'=>Mage::helper('themeoptions')->__('Pie')),
            array('value'=>'bar', 'label'=>Mage::helper('themeoptions')->__('Bar'))            
        );
    }

}?>