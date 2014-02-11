<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2008-2012 Amasty (http://www.amasty.com)
* @package Amasty_Meta
*/  
class Amasty_Meta_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    protected function _prepareLayout()
    {
        $product = $this->getProduct();
	    if (!$product){
            return parent::_prepareLayout();
        }
        
        if (!Mage::getStoreConfig('ammeta/product/enabled'))
            return parent::_prepareLayout();
            
        $hlp = Mage::helper('ammeta');    

        //templates configuration for products in categories
        $config = $hlp->getConfigByProduct($product);
        
        // product attribute => template name
        $pairs = array(
            'meta_title'        => 'title',
            'meta_description'  => 'description',
            'meta_keyword'      => 'keywords',
            'short_description' => 'short_description',
            'description'       => 'full_description',
            
        );
        foreach ($pairs as $attrCode => $patternName) {
            
            if ($product->getData($attrCode)){
                continue;
            }
            
            $pattern = Mage::getStoreConfig('ammeta/product/' . $patternName);
            foreach ($config as $item){
                if ($item->getData($patternName)){
                    // get first not empty pattern
                    $pattern = $item->getData($patternName);
                    break;
                }    
            }
            
            if ($pattern) {
                $tag = $hlp->parse($product, $pattern);
                $max = (int)Mage::getStoreConfig('ammeta/general/max_' . $attrCode);
                if ($max) {
                    $tag = substr($tag, 0, $max);
                }
                $product->setData($attrCode, $tag);    
            }
                
        }
        
        return parent::_prepareLayout();
    }
}