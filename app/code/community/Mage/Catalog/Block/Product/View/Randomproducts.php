<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product description block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Randomproducts extends Mage_Catalog_Block_Product_Abstract
{
    protected $_product = null;

    function getProduct()
    {
        if (!$this->_product) {
            $this->_product = Mage::registry('product');
        }
        return $this->_product;
    }

    /**
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     */
    public function getRandomProductIds($product)
    {
        
        $productId=$product->getId();
        
        $categoryIds=$product->getCategoryIds();
        if (sizeof($categoryIds)==0)
            return null;
        $categoryId=$categoryIds[0];
        $category = Mage::getModel('catalog/category')->load($categoryId);    
        
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addCategoryFilter($category)            
            ->addAttributeToFilter('type_id','configurable');        
    
        $count=4;
        if ($count>$collection->getSize())
            $count=$collection->getSize();
        
        $randomProductIds=array();
        $collectionIds=$collection->getAllIds();
        
        
        for ($i=0; $i<$count-1; ){ // $count-1 : exclude self product
            $rnd=rand(0,$collection->getSize()-1);
            if ($productId==$collectionIds[$rnd]){
                continue;
            }
            $flag=true;
            
            for ($j=0; $j<sizeof($randomProductIds); $j++ ){
                
                if ($collectionIds[$rnd]==$randomProductIds[$j]) {
                      $flag=false;
                      break;
                }
            }
            
            
            if ($flag==true){
                
                $randomProductIds[]=$collectionIds[$rnd];
                $i++;
            }
                
        }
        
        return $randomProductIds;
        

    }
    
    
}
