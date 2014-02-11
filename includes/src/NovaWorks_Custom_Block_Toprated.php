<?php   
class Novaworks_Custom_Block_Toprated extends Mage_Core_Block_Template {   
    protected $count = 10;
    public function _construct() {
        $this->count = $this->getData('count');
        $this->block_id = $this->getData('id');
        $this->block_title = $this->getData('title');
        $this->button_title = $this->getData('button_title');
        $this->button_link = $this->getData('button_link');        
    }
	public function getProductList() {
    	$_products = Mage::getResourceModel('reports/product_collection')
        	->addAttributeToSelect('*')
        	->joinField('rating_summary', 'review/review_aggregate', 'rating_summary', 'entity_pk_value=entity_id',  array('entity_type' => 1, 'store_id' => Mage::app()->getStore()->getId()), 'left') 
        	->setOrder('rating_summary', 'desc')          
        	->setPageSize($this->getCount());
    	return $_products;
   
	}
	protected function getCount() {
        return $this->count;
    }
	protected function getBlockID() {
        return $this->block_id;
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