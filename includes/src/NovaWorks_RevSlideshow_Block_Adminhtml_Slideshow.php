<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Block_Adminhtml_Slideshow extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_controller = 'adminhtml_slideshow';
		$this->_blockGroup = 'revslideshow';
		$this->_headerText = $this->__('RevSlideshow / Slides');
	}
}