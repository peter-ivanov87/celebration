<?php
class NovaWorks_FacebookConnect_Model_FacebookConnect extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('facebookconnect/facebookconnect');
    }
}