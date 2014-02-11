<?php
class NovaWorks_FacebookConnect_Block_Links extends Mage_Core_Block_Template
{

    public function addFbLink() {
        if ($parentBlock = $this->getParentBlock()) {
            $count = $this->helper('checkout/cart')->getSummaryCount();

            if ($count == 1) {
                $text = $this->__('My Cart (%s item)', $count);
            } elseif ($count > 0) {
                $text = $this->__('My Cart (%s items)', $count);
            } else {
                $text = $this->__('My Cart');
            }
        }
        return $this;
    }

}