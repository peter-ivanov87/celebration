
<?php class NovaWorks_ThemeOptions_Model_Menutype
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'default-menu', 'label'=>Mage::helper('themeoptions')->__('Default Menu')),
            array('value'=>'wide-menu', 'label'=>Mage::helper('themeoptions')->__('Wide Menu')),      
        );
    }

}
?>