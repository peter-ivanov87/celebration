<?php
/**
 * Free Lunch Labs
 * Author: Charles Drew
 * Email: charles@freelunchlabs.com
 *
 */

class FreeLunchLabs_Newslettercheckbox_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isEnabled() {
        return Mage::getStoreConfigFlag('newsletter/newslettercheckbox/enabled');
    }

    public function isCheckedByDefault() {
        return Mage::getStoreConfigFlag('newsletter/newslettercheckbox/checked');
    }

    public function ifSubscribed() {
        return Mage::getStoreConfigFlag('newsletter/newslettercheckbox/if_subscribed');
    }

    public function allowUnsubscribe() {
        return Mage::getStoreConfigFlag('newsletter/newslettercheckbox/allow_unsubscribe');
    }

    public function getCheckboxLabel() {
        return Mage::getStoreConfig('newsletter/newslettercheckbox/checkbox_label');
    }

//    public function isAvailableGuest() {
//        return Mage::getStoreConfigFlag('newsletter/newslettercheckbox/available_guest');
//    }
//
//    public function isAvailableRegister() {
//        return Mage::getStoreConfigFlag('newsletter/newslettercheckbox/available_register');
//    }
//
//    public function isAvailableCustomer() {
//        return Mage::getStoreConfigFlag('newsletter/newslettercheckbox/available_customer');
//    }

}