<?php
/**
 * Free Lunch Labs
 * Author: Charles Drew
 * Email: charles@freelunchlabs.com
 *
 */

class FreeLunchLabs_Newslettercheckbox_Block_Newsletter extends Mage_Checkout_Block_Onepage_Abstract {

    public function __construct() {
        parent::__construct();
        $this->setTemplate('newslettercheckbox/newsletter.phtml');
    }

    public function hideIfSubscribed() {
        if (Mage::helper('newslettercheckbox')->ifSubscribed()) {
            if ($this->isCustomerLoggedIn()) {
                $customer = $this->getCustomer();
                $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());

                if ($subscriber->getId() && $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    public function showToUser() {
        return true;
//        switch ($this->getQuote()->getCheckoutMethod()) {
//            case "guest":
//                if (Mage::helper('newslettercheckbox')->isAvailableGuest()) {
//                    return true;
//                } else {
//                    return false;
//                }
//                break;
//            case "register":
//                if (Mage::helper('newslettercheckbox')->isAvailableRegister()) {
//                    return true;
//                } else {
//                    return false;
//                }
//                break;
//            default:
//                if (Mage::helper('newslettercheckbox')->isAvailableCustomer()) {
//                    return true;
//                } else {
//                    return false;
//                }
//                break;
//        }
    }

}