<?php
/**
 *
 * @category   Yoast
 * @package    Yoast_MetaRobots
 * @copyright  Copyright (c) 2009-2010 Yoast (http://www.yoast.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Yoast
 * @package    Yoast_MetaRobots
 * @author     Yoast <magento@yoast.com>
 */
class Yoast_MetaRobots_Block_Catalog_Product_View extends Amasty_Meta_Block_Catalog_Product_View

 {
    protected function _prepareLayout()
    {
       
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $product = $this->getProduct();
		$robots = $product->getMetaRobots();
		if ($robots) {
			$headBlock->setRobots($robots);
            } else {
                $headBlock->setRobots($product->getMetaRobots());
            }
        }
        parent::_prepareLayout();
    }
 }