<?php   
class Novaworks_Custom_Block_Catfilter extends Mage_Catalog_Block_Product_List {   
    protected $count = 10;
    public function _construct() {
        $this->count = $this->getData('count');
        $this->block_id = $this->getData('id');
        $this->cat_id = $this->getData('cat');
        $this->block_title = $this->getData('title');
        $this->button_title = $this->getData('button_title');
        $this->button_link = $this->getData('button_link');
        $this->cat_selected = $this->getData('cat_selected');
        
    }
	public function getProductList($catid) {
		$_productCollection = Mage::getModel('catalog/product')
                ->getCollection()
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array('in' => array('finset' => $catid)))
                ->addAttributeToSelect('*')
                ->setPageSize($this->getCount());
    	return $_productCollection;
	}
    public function getCategoryName($id){
	    $catagory_model = Mage::getModel('catalog/category');
	    $categories = $catagory_model->load($id); // where $id will be the known category id
	    return $categories->getName();
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
	protected function getSelected() {
        return $this->cat_selected;
    }
}
