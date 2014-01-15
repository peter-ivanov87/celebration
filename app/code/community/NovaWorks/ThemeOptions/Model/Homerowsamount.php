<?php class NovaWorks_ThemeOptions_Model_Homerowsamount
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('themeoptions')->__('1 row')),
            array('value'=>2, 'label'=>Mage::helper('themeoptions')->__('2 rows')),
            array('value'=>3, 'label'=>Mage::helper('themeoptions')->__('3 rows'))         
        );
    }

}?>