<?php class NovaWorks_ThemeOptions_Model_Shopingcart
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'standard', 'label'=>Mage::helper('themeoptions')->__('Standard')),
            array('value'=>'accordion', 'label'=>Mage::helper('themeoptions')->__('Accordion'))         
        );
    }

}?>