<?php
/**
 * Free Lunch Labs
 * Author: Charles Drew
 * Email: charles@freelunchlabs.com
 *
 */

class FreeLunchLabs_Newslettercheckbox_Model_Checkout_Type_Onepage extends Mage_Checkout_Model_Type_Onepage {

    public function saveBilling($data, $customerAddressId) {
        if (isset($data['subscribed'])) {
            if (!empty($data['subscribed'])) {
                $this->getCustomerSession()->setIsSubscribed("opt-in");
            } else {
                $this->getCustomerSession()->setIsSubscribed("opt-out");
            }
        }
        return parent::saveBilling($data, $customerAddressId);
    }

}