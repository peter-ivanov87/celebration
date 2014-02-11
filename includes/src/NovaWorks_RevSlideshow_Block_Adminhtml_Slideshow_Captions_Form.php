<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Block_Adminhtml_Slideshow_Captions_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(
			array(
				'id' => 'captions_form',
				'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
				'method' => 'post',
				'enctype' => 'multipart/form-data'
			)
		);
		
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
}
