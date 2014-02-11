<?php
class Amasty_Meta_Block_Catalog_Category_Afterproduct extends Mage_Core_Block_Template
{
	/**
	 * Initialize template
	 *
	 */
	protected function _construct()
	{
		$this->setTemplate('amasty/ammeta/catalog/category/afterproduct.phtml');
	}

	protected function _toHtml()
	{
		$category = Mage::registry('current_category');
		if ($category->getData('display_mode') == Mage_Catalog_Model_Category::DM_PAGE || ! $category->getData('after_product_text')) {
			return '';
		}

		$this->setData('text', $category->getData('after_product_text'));

		return parent::_toHtml();
	}

}
