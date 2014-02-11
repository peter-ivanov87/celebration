<?php class NovaWorks_RevSlideshow_Model_Navigationtype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'none', 'label'=>Mage::helper('revslideshow')->__('None')),
            array('value'=>'bullet', 'label'=>Mage::helper('revslideshow')->__('Bullet')),      
            array('value'=>'thumb', 'label'=>Mage::helper('revslideshow')->__('Thumb')),     
            array('value'=>'both', 'label'=>Mage::helper('revslideshow')->__('Both'))
        );
    }

}
?>