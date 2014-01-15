<?php
class NovaWorks_Imagezoom_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getZoomConfig()
	{

		$zoom_options = Mage::getStoreConfig('imagezoomconfig/imagezoom_options');
		$zoom_cfg = '';
		$cnt = 0;
		
		switch($zoom_options['zoomeffect'])
		{
			case '1':
				$zoom_options['tint'] = "'#".$zoom_options['tintColor']."'";
				
				break;
			case '2':
				$zoom_options['softFocus'] = true;
				break;
		}

		foreach ($zoom_options as $key => $value )
		{
			if ($value != '' && $key != 'img_size' && $key != 'tintColor')
			{
				$cnt++;
				
				$zoom_cfg .= ($cnt != 1 ) ? ',' : '';
				$zoom_cfg .= $key.':'.$value;
			}
		}

		return $zoom_cfg;
	}

	public function getAllImages($product)
	{
		$productID = Mage::getModel('catalog/product')->load($product->getId());
		
		if ($productID['media_gallery'])
		{
			$images = new Varien_Data_Collection();
			
			foreach ($productID->getMediaGallery('images') as $image)
			{
				$image['url'] = $productID->getMediaConfig()->getMediaUrl($image['file']);
				$image['id'] = isset($image['value_id']) ? $image['value_id'] : null;
				$image['path'] = $productID->getMediaConfig()->getMediaPath($image['file']);
				$image['main_image'] = ($productID['image'] == $image['file']) ? true : false;
				$images->addItem(new Varien_Object($image));
			}
		
			$productID->setData('media_gallery_images', $images);
		}
		
		return $productID->getData('media_gallery_images');
	}
}
?>