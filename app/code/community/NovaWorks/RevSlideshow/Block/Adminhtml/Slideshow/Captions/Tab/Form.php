<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Block_Adminhtml_Slideshow_Captions_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Retrieve Additional Element Types
	 *
	 * @return array
	*/
	protected function _getAdditionalElementTypes()
	{
		return array(
			'image' => Mage::getConfig()->getBlockClassName('revslideshow/adminhtml_slideshow_helper_image')
		);
	}
	
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('slideshow_');
        $form->setFieldNameSuffix('slideshow');
        
		$this->setForm($form);
		$fieldset = $form->addFieldset('slideshow_general', array('legend'=> $this->__('Add Slide Captions')));
		$this->_addElementTypes($fieldset);
    $fieldset->addType('layer','NovaWorks_RevSlideshow_Block_Adminhtml_Slideshow_Captions_Form_Element_Layer');
      $fieldset->addField('layer', 'layer', array(
          'label'     => Mage::helper('lookbook')->__('Image'),
          'name'      => 'layer',
          'required'  => true,       
    ));
		$fieldset->addField('captions_field', 'hidden', array(
			'name' 		=> 'captions_field',
			'label' 	=> $this->__('Captions'),
			'title' 	=> $this->__('Captions'),
			'required'	=> true,
			'class'		=> 'required-entry',
		));
		
		if ($slideshow = Mage::registry('revslideshow_slideshow')) {
			$form->setValues($slideshow->getData());
		}

		return parent::_prepareForm();
	}

	/**
	 * Retrieve an array of all of the stores
	 *
	 * @return array
	 */
	protected function _getGroups()
	{
		$groups = Mage::getResourceModel('revslideshow/group_collection');
		$options = array('' => $this->__('-- Please Select --'));
		
		foreach($groups as $group) {
			$options[$group->getId()] = $group->getTitle();
		}

		return $options;
	}
}
