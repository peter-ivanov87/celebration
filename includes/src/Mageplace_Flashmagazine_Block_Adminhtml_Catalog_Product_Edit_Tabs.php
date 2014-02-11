<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
	protected function _prepareLayout()
	{
		$parent = parent::_prepareLayout();

		if(Mage::getSingleton('admin/session')->isAllowed('admin/flashmagazine/attach_magazine')) {
			$product = $this->getProduct();

			if (!($setId = $product->getAttributeSetId())) {
				$setId = $this->getRequest()->getParam('set', null);
			}

			if ($setId) {
				$this->addTab('flashmagazines',
					array(
						'label'		=> Mage::helper('flashmagazine')->__('Flash Flipping Books'),
						'url'		=> $this->getUrl('*/flashmagazine/product', array('_current' => true)),
						'class'		=> 'ajax',
					)
				);
			}
		}

		return $parent;
	}

	/**
	 * Processing block html after rendering
	 *
	 * @param   string $html
	 * @return  string
	 */
	protected function _afterToHtml($html)
	{
		$product = $this->getProduct();

		$html  = parent::_afterToHtml($html);
		$html .= "
		<script type=\"text/javascript\">
		MageplaceFlashmagazine = {};
		MageplaceFlashmagazine.Magazine = Class.create();
		MageplaceFlashmagazine.Magazine.prototype = {
			productId : {$product->getId()},

			initialize : function() {},

			setRelation : function(magazineId, productAttached) {
				new Ajax.Request('{$this->getUrl('*/flashmagazine/attach')}',
				{
					parameters: {
						product_id: this.productId,
						magazine_id: magazineId,
						product_attached: productAttached,
					},
					evalScripts: true,
					onSuccess: function(response) {
						this.complete(response);
					}.bind(this)
				});
			},

			complete : function(transport) {
				flashmagazine_magazine_gridJsObject.doFilter();
			}
		}

		var flashmagazineJs = new MageplaceFlashmagazine.Magazine();
		</script>
		";


		return $html;
	}
}
