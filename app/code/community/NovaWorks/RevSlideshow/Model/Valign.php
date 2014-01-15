<?php class NovaWorks_RevSlideshow_Model_Valign
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'top', 'label'=>Mage::helper('revslideshow')->__('Top')),
            array('value'=>'center', 'label'=>Mage::helper('revslideshow')->__('Center')),      
            array('value'=>'bottom', 'label'=>Mage::helper('revslideshow')->__('Bottom'))
        );
    }

}
?>