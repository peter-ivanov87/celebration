<?php
/**
 * Free Lunch Labs
 * Author: Charles Drew
 * Email: charles@freelunchlabs.com
 *
 */

class FreeLunchLabs_Newslettercheckbox_Model_Observer {

    protected function _subscribeCheckout($email) {
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
        if (Mage::getSingleton('customer/session')->getIsSubscribed() == "opt-in") {
            if (!$subscriber->getId() || $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED || $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                Mage::getModel('newsletter/subscriber')->subscribe($email);
            }
        } elseif (Mage::getSingleton('customer/session')->getIsSubscribed() == "opt-out") {
            if ($subscriber->getId() && Mage::helper('newslettercheckbox')->allowUnsubscribe()) {
                if ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED || $subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    $subscriber->unsubscribe();
                }
            }
        }
    }

    public function handleSubscription($observer) {
        $quote = $observer->getEvent()->getQuote();
        $customer = $quote->getCustomer();

        switch ($quote->getCheckoutMethod()) {
            case "guest":
                $this->_subscribeCheckout($quote->getBillingAddress()->getEmail());
                break;
            default:
                $this->_subscribeCheckout($customer->getEmail());
                break;
        }
    }

}

?>
