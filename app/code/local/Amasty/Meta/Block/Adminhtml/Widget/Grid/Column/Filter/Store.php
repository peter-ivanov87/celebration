<?php
class Amasty_Meta_Block_Adminhtml_Widget_Grid_Column_Filter_Store
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Store
{

    /**
     * Render HTML of the element
     *
     * @return string
     */
    public function getHtml()
    {
		$columnValue = $this->getColumn()->getValue();
		$addToHtml = '<option value="0" ' . ($columnValue === 0 ? ' selected="selected"' : '') . '>' .
					 Mage::helper('ammeta')->__('Default')
					 . '</option>';

		$html = parent::getHtml();

		return preg_replace('/^(\<select.+?\<\/option\>)/', '$1' . $addToHtml, $html);
    }

}
