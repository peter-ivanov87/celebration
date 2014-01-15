<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class NovaWorks_Ajax_IndexController extends Mage_Checkout_CartController
{
	public function addAction()
	{
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		if($params['isAjax'] == 1){
			$response = array();
			try {
				if (isset($params['qty'])) {
					$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				$product = $this->_initProduct();
				$related = $this->getRequest()->getParam('related_product');

				/**
				 * Check product availability
				 */
				if (!$product) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Unable to find Product ID');
				}

				$cart->addProduct($product, $params);
				if (!empty($related)) {
					$cart->addProductsByIds(explode(',', $related));
				}

				$cart->save();

				$this->_getSession()->setCartWasUpdated(true);

				/**
				 * @todo remove wishlist observer processAddToCart
				 */
				Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
				);

				if (!$cart->getQuote()->getHasError()){
					$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
					$response['status'] = 'SUCCESS';
					$response['message'] = $message;
					//New Code Here
					$this->loadLayout();
					$sidebar_block = $this->getLayout()->getBlock('top_cart_sidebar_replace');
					Mage::register('referrer_url', $this->_getRefererUrl());
					$sidebar = $sidebar_block->toHtml();
					$response['sidebar'] = $sidebar;
				}
			} catch (Mage_Core_Exception $e) {
				$msg = "";
				if ($this->_getSession()->getUseNotice(true)) {
					$msg = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$msg .= $message.'<br/>';
					}
				}

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add the item to shopping cart.');
				Mage::logException($e);
			}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
			return;
		}else{
			return parent::addAction();
		}
	}
	public function updateItemOptionsAction()
	{
        $cart   = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Quote item is not found.');
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            Mage::dispatchEvent('checkout_cart_update_item_complete',
                array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
					$response['status'] = 'SUCCESS';
					$response['message'] = $message;
					//New Code Here
					$this->loadLayout();
					$sidebar_block = $this->getLayout()->getBlock('top_cart_sidebar_replace');
					Mage::register('referrer_url', $this->_getRefererUrl());
					$sidebar = $sidebar_block->toHtml();
					$response['sidebar'] = $sidebar;
                }
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $msg .= $message.'<br/>';
                }
            }

				$response['status'] = 'ERROR';
				$response['message'] = $msg;
        } catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Cannot add the item to shopping cart.');
				Mage::logException($e);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
	}
	public function optionsAction(){
		$productId = $this->getRequest()->getParam('product_id');
		// Prepare helper and params
		$viewHelper = Mage::helper('catalog/product_view');

		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);

		// Render page
		try {
			$viewHelper->prepareAndRender($productId, $this, $params);
		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}
	public function deleteAction()
	{
		$id = (int) $this->getRequest()->getParam('id');  
		$response = array();
		if ($id) {
			try {  
				$this->_getCart()->removeItem($id)
					->save();  
				$this->loadLayout(); 
				$sidebar_block = $this->getLayout()->getBlock('top_cart_sidebar_replace');
				$sidebar = $sidebar_block->toHtml();
				$this->getResponse()->setBody($output);
				Mage::register('referrer_url', $this->_getRefererUrl());
				$message = $this->__('Product removed to your shopping cart.');
				$response['status'] = 'SUCCESS';
				$response['message'] = $message;
				$response['sidebar'] = $sidebar;
			} catch (Exception $e) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->_getSession()->addError($this->__('Cannot remove the item.')); 
				Mage::logException($e);
			}
		} 
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response)); 
	}
	public function compareAction(){
		$response = array(); 
		if ($productId = (int) $this->getRequest()->getParam('product')) {
			$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId); 
			if ($product->getId()/* && !$product->isSuper()*/) {
				Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
				$response['status'] = 'SUCCESS';
				$response['message'] = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
				Mage::register('referrer_url', $this->_getRefererUrl());
				Mage::helper('catalog/product_compare')->calculate();
				Mage::dispatchEvent('catalog_product_compare_add_product', array('product'=>$product));
				$this->loadLayout();
				$sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
				$sidebar = $sidebar_block->toHtml();
				$response['sidebar'] = $sidebar;
				$dropdown_block = $this->getLayout()->getBlock('compare_dropdown');
				$dropdown = $dropdown_block->toHtml();
				$response['dropdown'] = $dropdown;
			}
		}
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback'). Mage::helper('core')->jsonEncode($response));
		return;
	} 
public function removecompareAction()
	{
		$response = array();
		if ($productId = (int) $this->getRequest()->getParam('product')) {
			$product = Mage::getModel('catalog/product')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($productId); 
			if($product->getId()) { 
				$item = Mage::getModel('catalog/product_compare_item'); 
				if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }
				$item->loadByProduct($product); 
				if($item->getId()) {
					$item->delete();
					$response['status'] = 'SUCCESS';
					$response['message'] = $this->__('The product %s has been removed from comparison list.', $product->getName()); 
					Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
					Mage::helper('catalog/product_compare')->calculate();
					$this->loadLayout();
					$sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
					$sidebar = $sidebar_block->toHtml();
					$response['sidebar'] = $sidebar;
					$dropdown_block = $this->getLayout()->getBlock('compare_dropdown');
					$dropdown = $dropdown_block->toHtml();
					$response['dropdown'] = $dropdown;
				}
			}
		} 
		$this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback'). Mage::helper('core')->jsonEncode($response));
		return;  
	}
	public function clearcompareAction(){
        $response = array();
        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('catalog/session');

        try {
            $items->clear();
            $session->addSuccess($this->__('The comparison list was cleared.'));
            $response['status'] = 'SUCCESS';
            $response['message'] = $this->__('The comparison list was cleared.');
            Mage::helper('catalog/product_compare')->calculate();
            $this->loadLayout();
					$sidebar_block = $this->getLayout()->getBlock('catalog.compare.sidebar');
					$sidebar = $sidebar_block->toHtml();
					$response['sidebar'] = $sidebar;
					$dropdown_block = $this->getLayout()->getBlock('compare_dropdown');
					$dropdown = $dropdown_block->toHtml();
					$response['dropdown'] = $dropdown;
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while clearing comparison list.'));
        }
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback'). Mage::helper('core')->jsonEncode($response));
        return;
    }	
	protected function _getWishlist($wishlistId = null)
	{
		$wishlist = Mage::registry('wishlist');
		if ($wishlist) {
			return $wishlist;
		} 
		try {
			if (!$wishlistId) {
				$wishlistId = $this->getRequest()->getParam('wishlist_id');
			}
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
			/* @var Mage_Wishlist_Model_Wishlist $wishlist */
			$wishlist = Mage::getModel('wishlist/wishlist');
			if ($wishlistId) {
				$wishlist->load($wishlistId);
			} else {
				$wishlist->loadByCustomer($customerId, true);
			}
	
			if (!$wishlist->getId() || $wishlist->getCustomerId() != $customerId) {
				$wishlist = null;
				Mage::throwException(
				Mage::helper('wishlist')->__("Requested wishlist doesn't exist")
				);
			}
	
			Mage::register('wishlist', $wishlist);
		} catch (Mage_Core_Exception $e) {
			Mage::getSingleton('wishlist/session')->addError($e->getMessage());
			return false;
		} catch (Exception $e) {
			Mage::getSingleton('wishlist/session')->addException($e,
			Mage::helper('wishlist')->__('Wishlist could not be created.')
			);
			return false;
		}
	
		return $wishlist;
	}
	public function addwishlistAction()
	{ 
		$response = array();
		if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Wishlist Has Been Disabled By Admin');
		}
		if(!Mage::getSingleton('customer/session')->isLoggedIn()){
			$response['status'] = 'ERROR';
			$response['message'] = $this->__('Please Login First');
		}

		if(empty($response)){
			$session = Mage::getSingleton('customer/session');
			$wishlist = $this->_getWishlist();
			if (!$wishlist) {
				$response['status'] = 'ERROR';
				$response['message'] = $this->__('Unable to Create Wishlist');
			}else{

				$productId = (int) $this->getRequest()->getParam('product');
				if (!$productId) {
					$response['status'] = 'ERROR';
					$response['message'] = $this->__('Product Not Found');
				}else{

					$product = Mage::getModel('catalog/product')->load($productId);
					if (!$product->getId() || !$product->isVisibleInCatalog()) {
						$response['status'] = 'ERROR';
						$response['message'] = $this->__('Cannot specify product.');
					}else{ 
						try {
							$requestParams = $this->getRequest()->getParams();
							$buyRequest = new Varien_Object($requestParams);

							$result = $wishlist->addNewItem($product, $buyRequest);
							if (is_string($result)) {
								Mage::throwException($result);
							}
							$wishlist->save();

							Mage::dispatchEvent(
                				'wishlist_add_product',
							array(
			                    'wishlist'  => $wishlist,
			                    'product'   => $product,
			                    'item'      => $result
							)
							);

							Mage::helper('wishlist')->calculate();

							$message = $this->__('%1$s has been added to your wishlist.', $product->getName());
							$response['status'] = 'SUCCESS';
							$response['message'] = $message; 
							Mage::unregister('wishlist'); 
							$sidebar_block = '<div id="count-wishlist-'.$productId.'" class="count-wishlist-box"><i class="icon-heart liked"></i>'.$this->CountWishilist($productId).'</div>';
							$wishlist_header = '<div class="span6 wishlist-link"><a title="'.$this->__('My Wishlist').'" href="'.Mage::getUrl('wishlist').'"><i class="icon-heart"></i>'.$this->__('My Wishlist').' ('.Mage::helper('wishlist')->getItemCount().')</a></div>';
							$response['sidebar'] = $sidebar_block;
							$response['wishlist_header'] = $wishlist_header;
						}
						catch (Mage_Core_Exception $e) {
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist: %s', $e->getMessage());
						}
						catch (Exception $e) {
							mage::log($e->getMessage());
							$response['status'] = 'ERROR';
							$response['message'] = $this->__('An error occurred while adding item to wishlist.');
						}
					}
				}
			}

		}

        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody( (string) $this->getRequest()->getParam('callback').Mage::helper('core')->jsonEncode($response));
		return;
	}
protected function CountWishilist($productID){
 	$wishlist = Mage::getModel('wishlist/item')->getCollection();
 	$wishlist->getSelect()
                  ->join(array('t2' => 'wishlist'),
                         'main_table.wishlist_id = t2.wishlist_id',
                         array('wishlist_id','customer_id'))
                         ->where('main_table.product_id = '.$productID);
        $count = $wishlist->count();
        $wishlist = Mage::getModel('wishlist/item')->getCollection();
     if($count >0) {return $count;} 
}	
	public function quickviewAction(){
		$productId = $this->getRequest()->getParam('product_id');
		// Prepare helper and params
		$viewHelper = Mage::helper('catalog/product_view');

		$params = new Varien_Object();
		$params->setCategoryId(false);
		$params->setSpecifyOptions(false);

		// Render page
		try {
			$viewHelper->prepareAndRender($productId, $this, $params);
		} catch (Exception $e) {
			if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
				if (isset($_GET['store'])  && !$this->getResponse()->isRedirect()) {
					$this->_redirect('');
				} elseif (!$this->getResponse()->isRedirect()) {
					$this->_forward('noRoute');
				}
			} else {
				Mage::logException($e);
				$this->_forward('noRoute');
			}
		}
	}	
	
	
}
