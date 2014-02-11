<?php
class Amasty_Meta_Block_Adminhtml_Widget_Grid_Column_Renderer_Category
	extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		return $row->getData('category_name');
	}
}
