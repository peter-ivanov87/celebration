<?php   
class Novaworks_Custom_Block_Cat extends Mage_Catalog_Block_Product_New {   
    protected $count = 10;
    public function _construct() {
        $this->count = $this->getData('count');
        $this->block_id = $this->getData('id');
        $this->cat_id = $this->getData('cat');
        $this->block_title = $this->getData('title');
        $this->button_title = $this->getData('button_title');
        $this->button_link = $this->getData('button_link');
        
    }
	public function getProductList() {
    	$_products = Mage::getResourceModel('catalog/product_collection')
    		->addAttributeToSelect('*')
    		->addCategoryFilter(Mage::getModel('catalog/category')->load($this->getCateoryID()))
    		->setPageSize($this->getCount());
    	return $_products;
	}
	protected function getCount() {
        return $this->count;
    }
	protected function getBlockID() {
        return $this->block_id;
    }
	protected function getCateoryID() {
        return $this->cat_id;
    }
	protected function getBlockTitle() {
        return $this->block_title;
    }
	protected function getButton_title() {
        return $this->button_title;
    }
	protected function getButton_link() {
        return $this->button_link;
    }	
}