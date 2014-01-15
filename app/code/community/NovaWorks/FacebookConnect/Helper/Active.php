<?php
class NovaWorks_FacebookConnect_Helper_Active extends Mage_Core_Helper_Abstract {

    public function getAppId() {
        return Mage::getStoreConfig('facebookconnect/settings/appid');
    }

    public function getSecretKey() {
        return Mage::getStoreConfig('facebookconnect/settings/secret');
    }

    public function getProducts($order) {
        $db_read = Mage::getSingleton('core/resource')->getConnection('facebookconnect_read');
        $tablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $sql = 'SELECT `product_id` FROM `' . $tablePrefix . 'sales_flat_order_item` as i
                LEFT JOIN `' . $tablePrefix . 'sales_flat_order` as o ON o.`increment_id` = "' . $order . '"
                WHERE i.`order_id` = o.`entity_id` AND i.`parent_item_id` IS NULL';
        $data = $db_read->fetchAll($sql);
        return $data;
    }          
        
}
