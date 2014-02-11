<?php
/**
 * @copyright   Copyright (c) 2009-2011 Amasty (http://www.amasty.com)
 */
class Amasty_Meta_Model_Observer
{
	protected $_cache = array();

	/** @var  Amasty_Meta_Helper_Data */
	protected $_helper;

	public function __construct()
	{
		$this->_helper = Mage::helper('ammeta');
	}

	/**
	 * Observe category page
	 *
	 * @param $observer
	 */
	public function setCategoryData($observer)
	{
		//return;
		if (! Mage::getStoreConfig('ammeta/cat/enabled')) {
			return;
		}

		$cat = $observer->getEvent()->getCategory();
		$cat->setCategory(new Varien_Object(array('name' => $cat->getName())));


		//assign attributes
		$attributes = Mage::getSingleton('catalog/layer')->getFilterableAttributes();
		foreach ($attributes as $a) {
			$code = $a->getAttributeCode();
			$v    = Mage::app()->getRequest()->getParam($code);
			if (is_numeric($v)) {
				$v = $a->getFrontend()->getOption($v);
				$cat->setData($code, $v);
			}
		}

		$path = Mage::helper('catalog')->getBreadcrumbPath();
		if (count($path) > 1) { // child
			//assign parent name
			$title = array();
			foreach ($path as $breadcrumb) {
				$title[] = $breadcrumb['label'];
			}
			array_pop($title); // category itself
			$cat->setData('meta_parent_category', array_pop($title));
		}

		$pathIds = array_reverse($cat->getPathIds());
		array_shift($pathIds);

		$configFromUrl = Mage::helper('ammeta')->getMetaConfigByUrl();
		$configData    = null;

		$replace = array(
			'meta_title',
			'meta_keywords',
			'meta_description',
			'description',
			'h1_tag',
			'image_alt',
			'image_title',
			'after_product_text'
		);
		foreach ($replace as $key) {
			if ($cat->getData($key)) {
				continue;
			}

			$pattern = null;
			if (! empty($configFromUrl[$key])) {
				$pattern = $configFromUrl[$key];
			} else {
				if (! $configData) {
					$configData = $this->_getConfigData($pathIds, $replace);
				}

				if (! empty($configData[$key])) {
					$pattern = $configData[$key];
				}
			}
			if (! $pattern) {
				continue;
			}

			Mage::helper('ammeta')->addEntityToCollection($cat);
			$tag = Mage::helper('ammeta')->parse($pattern);
			$max = (int) Mage::getStoreConfig('ammeta/general/max_' . $key);
			if ($max) {
				$tag = substr($tag, 0, $max);
			}

			$cat->setData($key, $tag);
		}
	}

	public function pageBlockObserverBefore(Varien_Event_Observer $observer)
	{
		$block = $observer->getEvent()->getBlock();

		if ($block instanceof Mage_Catalog_Block_Product_View) {
			$this->_observeProductPage($block);
		} elseif ($block instanceof Mage_Page_Block_Html_Head) {
			$this->_observeHtmlHead($block);
		}

		return true;
	}

	public function pageBlockObserverAfter(Varien_Event_Observer $observer)
	{
		$block     = $observer->getEvent()->getBlock();
		$transport = $observer->getEvent()->getTransport();

		if ($block instanceof Mage_Page_Block_Html) {
			$this->_observeHtml($block, $transport);
		}

		return true;
	}

	/**
	 * Product page observer
	 *
	 * @param Mage_Core_Block_Template $block
	 *
	 * @return bool
	 */
	protected function _observeProductPage(Mage_Core_Block_Template $block)
	{
		$product = $block->getProduct();
		if (! $product || ! Mage::getStoreConfig('ammeta/product/enabled')) {
			return false;
		}

		$catPaths       = array();
		$categories     = $product->getCategoryCollection();
		$i              = 0;
		$maxLengthIndex = 0;
		$maxLength      = 0;
		foreach ($categories as $category) {
			$catPaths[] = $category->getPathIds();
			if (count($category->getPathIds()) > $maxLength) {
				$maxLengthIndex = $i;
				$maxLength      = count($category->getPathIds());
			}
			$i ++;
		}

		$catPaths = $catPaths[$maxLengthIndex];

		// product attribute => template name
		$attributes = array(
			'meta_title',
			'meta_description',
			'meta_keyword',
			'short_description',
			'description',
			'h1_tag'
		);

		$configFromUrl = $this->_helper->getMetaConfigByUrl();

		$configData = null;
		foreach ($attributes as $attrCode) {
			if ($product->getData($attrCode)) {
				continue;
			}

			$configItem = null;
			if (! empty($configFromUrl[$attrCode])) {
				$configItem = $configFromUrl[$attrCode];
			} else {
				if (! $configData) {
					$configData = $this->_getConfigData($catPaths, $attributes, 'product_', 'pr');
				}

				$configItem = $configData[$attrCode];
			}

			if ($configItem) {
				$this->_helper->addEntityToCollection($product);
				$tag = $this->_helper->parse($configItem);
				$max = (int) Mage::getStoreConfig('ammeta/general/max_' . $attrCode);
				if ($max) {
					$tag = substr($tag, 0, $max);
				}
				$product->setData($attrCode, $tag);
			}
		}
	}

	/**
	 * Observe HEAD on all website
	 *
	 * @param Mage_Core_Block_Template $block
	 */
	protected function _observeHtmlHead(Mage_Core_Block_Template $block)
	{
		$configFromUrl = $this->_helper->getMetaConfigByUrl();

		$attributes = array(
			'meta_title'       => 'title',
			'meta_description' => 'description',
			'meta_keywords'    => 'keywords',
			'meta_robots'      => 'robots'
		);

		foreach ($attributes as $key => $attr) {
			if (! empty($configFromUrl[$key])) {
				$configFromUrl[$key] = Mage::helper('ammeta')->parse($configFromUrl[$key]);
				$block->setData($attr, $configFromUrl[$key], true);
			}
		}
	}

	protected function _observeHtml(Mage_Core_Block_Template $block, Varien_Object $transport)
	{
		$configFromUrl = $this->_helper->getMetaConfigByUrl();

		$tagValue = null;
		if (! empty($configFromUrl['custom_h1_tag'])) {
			$tagValue = $configFromUrl['custom_h1_tag'];
		}

		/**
		 * Replace h1 in category and product page
		 */
		if (! $tagValue && $contentBlock = $block->getChild('content')) {
			if ($productBlock = $contentBlock->getChild('product.info')) {
				if ($product = $productBlock->getProduct()) {
					$h1 = $product->getData('h1_tag');
					if (! empty($h1)) {
						$tagValue = $h1;
					}
				}
			} elseif ($categoryBlock = $contentBlock->getChild('category.products')) {
				if ($category = $categoryBlock->getCurrentCategory()) {
					$h1 = $category->getData('h1_tag');
					if (! empty($h1)) {
						$tagValue = $h1;
					}

					$replaceAttributes = array();
					if ($imgAlt = $category->getData('image_alt')) {
						$replaceAttributes['alt'] = $imgAlt;
					}

					if ($imgTitle = $category->getData('image_title')) {
						$replaceAttributes['title'] = $imgTitle;
					}

					if (! empty($replaceAttributes)) {
						$html = $transport->getHtml();
						$this->_helper->replaceImageData($html, $replaceAttributes);
						$transport->setHtml($html);
					}
				}
			}
		}

		if ($tagValue) {
			$html = $transport->getHtml();
			$tagValue = Mage::helper('ammeta')->parse($tagValue);
			$this->_helper->replaceH1Tag($html, $tagValue);
			$transport->setHtml($html);
		}
	}

	/**
	 * @param $categoryPaths
	 * @param $keys
	 * @param string $startPrefix
	 * @param null $cacheKey
	 *
	 * @return array
	 */
	protected function _getConfigData($categoryPaths, $keys, $startPrefix = 'cat_', $cacheKey = null)
	{
		if ($cacheKey && isset($this->_cache[$cacheKey])) {
			return $this->_cache[$cacheKey];
		}

		$configData = Mage::getResourceModel('ammeta/config')->getRecursionConfigData(
			$categoryPaths, Mage::app()->getStore()->getId()
		);

		$firstParents = Mage::helper('ammeta')->getParentsId($categoryPaths);

		$resultData = array();
		if ($cacheKey) {
			$this->_cache[$cacheKey] = & $resultData;
		}

		foreach ($keys as $key) {
			foreach ($configData as $itemConfig) {
				$prefix = in_array($itemConfig['category_id'], $firstParents) ? '' : 'sub_';
				$prefix .= $startPrefix;
				if (! isset($resultData[$key]) && ! empty($itemConfig[$prefix . $key]) &&
					trim(! empty($itemConfig[$prefix . $key])) != ''
				) {

					if ($key == 'meta_description') {
						$itemConfig[$prefix . $key] =
							substr($itemConfig[$prefix . $key], 0, $this->_helper->getMaxMetaDescriptionLength());
					}

					if ($key == 'meta_title') {
						$itemConfig[$prefix . $key] =
							substr($itemConfig[$prefix . $key], 0, $this->_helper->getMaxMetaTitleLength());
					}

					$resultData[$key] = htmlentities($itemConfig[$prefix . $key]);
					break;
				}
			}
		}

		return $resultData;
	}


}