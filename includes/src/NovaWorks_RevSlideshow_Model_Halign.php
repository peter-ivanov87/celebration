<?php class NovaWorks_RevSlideshow_Model_Halign
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'left', 'label'=>Mage::helper('revslideshow')->__('Left')),
            array('value'=>'center', 'label'=>Mage::helper('revslideshow')->__('Center')),      
            array('value'=>'right', 'label'=>Mage::helper('revslideshow')->__('Right'))
        );
    }

}
?>