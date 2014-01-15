<?php class NovaWorks_RevSlideshow_Model_Navigationarrows
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'nexttobullets', 'label'=>Mage::helper('revslideshow')->__('With Bullets')),
            array('value'=>'solo', 'label'=>Mage::helper('revslideshow')->__('Solo')),      
            array('value'=>'none', 'label'=>Mage::helper('revslideshow')->__('None'))
        );
    }

}
?>