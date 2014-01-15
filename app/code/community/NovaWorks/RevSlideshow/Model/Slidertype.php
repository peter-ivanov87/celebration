<?php class NovaWorks_RevSlideshow_Model_Slidertype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('revslideshow')->__('Fixed')),
            array('value'=>2, 'label'=>Mage::helper('revslideshow')->__('Responsive')),      
            array('value'=>3, 'label'=>Mage::helper('revslideshow')->__('Full Width'))      
        );
    }

}
?>