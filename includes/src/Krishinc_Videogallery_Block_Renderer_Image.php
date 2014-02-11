<?php
class Krishinc_Videogallery_Block_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['image'])) return '';
		$image = Mage::getBaseDir('media').DS."videogallery".$row['image'];
		if (!file_exists($image)){
			$url = $row['videogallery_url'];
			parse_str( parse_url( $url, PHP_URL_QUERY ) );
			$videourl = 'http://img.youtube.com/vi/'.$v.'/0.jpg';
			$videoimage = $v;
			$img_file = $videourl;				
			$img_file=file_get_contents($img_file);
			$file_loc=Mage::getBaseDir('media').DS."videogallery".DS.'videogallery_'.$videoimage.'.jpg';
			
			$file_handler=fopen($file_loc,'w');
			
			if(fwrite($file_handler,$img_file)==false){
				echo 'error';
			}
			fclose($file_handler);
		}
 		$block = new Krishinc_videogallery_Block_Videogallery(); 
    	return '<img width="75px" height="55px" src="'.$block->getImageresize($row['image'],'small',75,55). '" />';
    }

}
