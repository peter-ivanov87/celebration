<?php
class Krishinc_Videogallery_Model_Videogallery extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('videogallery/videogallery');
    }
	public function updateOptionsToVideogallerys($options)
    {
    	
    	if($options)
    	{ 
     
    		foreach ($options['value'] as $optionId => $value)
    		{
    			$model = Mage::getModel('videogallery/videogallery');
    			$data = $model->load($optionId); 
    			if(!$data->getVideogalleryId())
    			{  
    				
    				$model->setVideogalleryId($optionId)
	    				 ->setVideogalleryOptionId($optionId)
	    				 ->setName($value[0]) 
	    				 ->save();
	    				
    	
    			} else {
    				
	    			$data->setName($value[0])
	    				 ->save();
    			}    			
    		}
    		  
    		foreach ($options['delete'] as $optionId => $value)
    		{ 
    			if(!empty($value))
    			{
    				$model->load($optionId);  
                    $image = $this->getImage();
                    $filepath = Mage::getBaseDir('media')."\videogallery\\".$image;  
                    unlink($filepath);                    
                    $model->delete();
    			}
			}
    		
		}
	} 
}