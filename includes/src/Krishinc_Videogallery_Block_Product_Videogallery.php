<?php

class Krishinc_Videogallery_Block_Product_Videogallery extends Mage_Catalog_Block_Product_View
{
	 
	protected $_videogalleryCollection;
	 
    public function getVideogallerysCollection($product)
    {
    	if($product)
    	{
    		$productVideogallerys = $product->getVideogallery();
    	  	$pos = strpos($productVideogallerys,',');
    		if ($pos === false)  
    		{
    			 
    			 $this->_videogalleryCollection[] = Mage::getModel('videogallery/videogallery')->getCollection()  
				                			->addFieldToFilter('videogallery_id', $productVideogallerys);
	   		} else {
    			
    			$arrProductVideogallerys = explode(',',$productVideogallerys);  
				foreach ($arrProductVideogallerys as $awId)
				{
					  $this->_videogalleryCollection[] = Mage::getModel('videogallery/videogallery')->getCollection()  
		                ->addFieldToFilter('videogallery_id', $awId); 
				}           
    		}
 
 
    	}
    
        return $this->_videogalleryCollection;
    }
}