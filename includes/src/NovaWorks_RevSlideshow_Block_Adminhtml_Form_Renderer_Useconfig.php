<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Block_Adminhtml_Form_Renderer_Useconfig extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{
	/**
	 * Add the 'Use Config Settings' checkbox to the input element
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$id = 'use_config_' . $element->getId();
		
		$checkbox = new Varien_Data_Form_Element_Checkbox(array('html_id' => $id, 'name' => $id));
		$checkbox->setForm($element->getForm());
		$checkbox->setAfterElementHtml(' <label for="' . $id . '">' . $this->helper('adminhtml')->__('Use Config Settings') . '</label>' . $this->getElementJs());
		
		$checkbox->setOnclick(sprintf("if(this.checked){\$('%s').disabled=true;}else{\$('%s').disabled=false;}", $element->getForm()->getHtmlIdPrefix() . $element->getId(), $element->getForm()->getHtmlIdPrefix() . $element->getId()));
		
		if (!$element->getValue()) {
			$checkbox->setChecked(true);
			$element->setDisabled(true);
			
			if (preg_match("/^carousel_([a-z_]{1,})$/", $element->getId(), $result)) {
				$element->setValue(Mage::getStoreConfig('revslideshow/carousel/' . $result[1]));
			}
		}

		$element->setNote($checkbox->getElementHtml());
		
		return parent::render($element);
	}
}
