<?php
class Krishinc_videogallery_Block_Videogallery extends Mage_Catalog_Block_Product_List
{

	public function getImageresize($image,$type,$width,$height)
    {
     		//IMAGE RESIZE CODE START
			if(!file_exists(Mage::getBaseDir('media').'/videogallery/resized/'.$type.'/'))mkdir(Mage::getBaseDir('media').'/videogallery/resized/'.$type.'/',0777);
			$imageUrl = Mage::getBaseDir('media').'/videogallery/'.$image;				
			if($imageUrl):
				$imageName = substr(strrchr($imageUrl,"/"),1);
				$imageResized = Mage::getBaseDir('media').DS."videogallery".DS."resized".DS."".$type."".DS.$imageName;
				$dirImg = Mage::getBaseDir().str_replace("/",DS,strstr($imageUrl,'/media'));
				if (!file_exists($imageResized)&&file_exists($dirImg)) :
					$imageObj = new Varien_Image($imageUrl);
					$imageObj->constrainOnly(FALSE);
					$imageObj->keepAspectRatio(FALSE);
					$imageObj->keepFrame(FALSE);
					$imageObj->resize($width, $height);
					$imageObj->save($imageResized);
				endif;
				return Mage::getBaseUrl('media')."videogallery/resized/".$type."/".$imageName; 
			endif;
			//IMAGE RESIZE CODE END
    }  
	public function _prepareLayout()
	{
		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		$breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Home Page'), 'link'=>Mage::getBaseUrl()));


		if($this->getRequest()->getParam('id')!='')
		{
			$breadcrumbs->addCrumb('videogallery', array('label'=>'video gallery', 'title'=>'video gallery', 'link'=>Mage::getUrl("videogallery")));
			$galleryid=$this->getRequest()->getParam('id');
			$name=strtolower($galleryid);
			
			$breadcrumbs->addCrumb(''.$name.'', array('label'=>''.$name.'', 'title'=>''.$name.''));
								
		}else
		{
				$breadcrumbs->addCrumb('videogallery', array('label'=>'video gallery', 'title'=>'video gallery'));
		}

		
		return parent::_prepareLayout();
	} 

}