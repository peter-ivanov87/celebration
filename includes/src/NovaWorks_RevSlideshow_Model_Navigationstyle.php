<?php class NovaWorks_RevSlideshow_Model_Navigationstyle
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'round', 'label'=>Mage::helper('revslideshow')->__('Round')),
            array('value'=>'old-round', 'label'=>Mage::helper('revslideshow')->__('Old Round')),      
            array('value'=>'old-square', 'label'=>Mage::helper('revslideshow')->__('Old Square')),
            array('value'=>'navbar-old', 'label'=>Mage::helper('revslideshow')->__('Old Navbar'))
        );
    }

}
?>