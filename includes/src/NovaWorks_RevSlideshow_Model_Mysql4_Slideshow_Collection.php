<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Model_Mysql4_Slideshow_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('revslideshow/slideshow');
	}

	/**
	 * Init collection select
	 *
	 * @return NovaWorks_RevSlideshow_Model_Mysql4_Slideshow_Collection
	*/
	protected function _initSelect()
	{
		$this->getSelect()->from(array('main_table' => $this->getMainTable()));
		
		return $this;
	}
	public function addStoreFilter($store, $withAdmin = true)
	{
	    if ($store instanceof Mage_Core_Model_Store) {
	        $store = array($store->getId());
	    }
	
	    $this->getSelect()->join(
	        array('store_table' => $this->getTable('revslideshow/stores')),
	        'main_table.slideshow_id = store_table.slideshow_id',
	        array()
	    )
	    ->where('store_table.store_id in (?)', ($withAdmin ? array(0, $store) : $store))
	    ->group('main_table.slideshow_id');
	
	    return $this;
	} 
}
