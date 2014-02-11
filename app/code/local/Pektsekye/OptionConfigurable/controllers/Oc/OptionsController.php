<?php
class Pektsekye_OptionConfigurable_Oc_OptionsController extends Mage_Adminhtml_Controller_Action
{

  protected function _initProduct()
  {
      $productId  = (int) $this->getRequest()->getParam('id');
      $product    = Mage::getModel('catalog/product')
          ->setStoreId($this->getRequest()->getParam('store', 0));

      if ($productId)
        $product->load($productId);

      Mage::register('current_product', $product);
  }  
   

  public function indexAction()
  {   
      $this->_initProduct();
      $this->loadLayout();
      $this->getResponse()->setBody(
          $this->getLayout()
              ->createBlock('optionconfigurable/oc_options', 'optionconfigurable')
              ->toHtml()
      );
  }


  public function optionimagesAction()
  {   
      $this->_initProduct();
      $this->loadLayout();
      $this->getResponse()->setBody(
          $this->getLayout()
              ->createBlock('optionconfigurable/oc_optionimages', 'optionconfigurable_images')
              ->toHtml()
      );
  }

}
