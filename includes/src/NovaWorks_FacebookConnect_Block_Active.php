<?php
class NovaWorks_FacebookConnect_Block_Active extends Mage_Core_Block_Template {

    public function getAppId()
    {
        return Mage::getStoreConfig('facebookconnect/settings/appid');
    }

    public function getSecretKey()
    {
        return Mage::getStoreConfig('facebookconnect/settings/secret');
    }
    public function checkFbUser()
    {
	$user_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
	$uid = 0;
	$db_read = Mage::getSingleton('core/resource')->getConnection('facebookconnect_read');
	$tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        
	$sql = 'SELECT `fb_id`
		FROM `'.$tablePrefix.'novaworks_facebook_customer`
		WHERE `customer_id` = '.$user_id.'
		LIMIT 1';
	$data = $db_read->fetchRow($sql);
	if (count($data)) {
	  $uid = $data['fb_id'];
	}
	return $uid;
    }	
}