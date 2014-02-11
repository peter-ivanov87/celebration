<?php
/**
 * @category    NovaWorks
 * @package     NovaWorks_RevSlideshow
 * @license     http://novaworks.net
 * @author      Dzung Nova <dzung@novaworks.vn>
 */

class NovaWorks_RevSlideshow_Block_Adminhtml_Slideshow_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
		
		$fieldset = $form->addFieldset('slideshow_general', array('legend'=> $this->__('General Information')));

		$this->_addElementTypes($fieldset);
     /**
     * Check is single store mode
     */
    if (!Mage::app()->isSingleStoreMode()) {
        $fieldset->addField('store_id', 'multiselect', array(
            'name'      => 'stores[]',
            'label'     => Mage::helper('cms')->__('Store View'),
            'title'     => Mage::helper('cms')->__('Store View'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        ));
    }
    else {
        $fieldset->addField('store_id', 'hidden', array(
            'name'      => 'stores[]',
            'value'     => Mage::app()->getStore(true)->getId()
        ));
        //$model->setStoreId(Mage::app()->getStore(true)->getId());
    }
		$fieldset->addField('title', 'text', array(
			'name' 		=> 'title',
			'label' 	=> $this->__('Slide Title'),
			'title' 	=> $this->__('Slide Title'),
			'required'	=> true,
			'class'		=> 'required-entry',
		));
		$fieldset->addField('slide_url', 'text', array(
			'name' 		=> 'slide_url',
			'label' 	=> $this->__('Slide Url'),
			'title' 	=> $this->__('Slide Url'),
			'required'	=> false,
			'after_element_html' => '<small>Link on the whole slide picture.</small>',
		));
$fieldset->addField('slide_target', 'select', array(
          'label'     => $this->__('Url Open In'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'slide_target',
          'onclick' => "",
          'onchange' => "",
          'value'  => '0',
          'values' => array(0=>'Same Window',1 => 'New Window'),
          'disabled' => false,
          'readonly' => false,
          'after_element_html' => '<small>The target of the slide link.</small>',
          'tabindex' => 1
        ));
$fieldset->addField('slide_transition', 'select', array(
          'label'     => $this->__('Transition'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'slide_transition',
          'onclick' => "",
          'onchange' => "",
          'value'  => '1',
          'values' => array('random'=>'Random','fade' => 'Fade','slidehorizontal' => 'Slide Horizontal', 'slidevertical' => 'Slide Vertical','boxslide'=>'Box Slide','boxfade' => 'Box Fade','slotzoom-horizontal' => 'SlotZoom Horizontal', 'slotslide-horizontal' => 'SlotSlide Horizontal','slotzoom-vertical'=>'SlotZoom Vertical','slotslide-vertical' => 'SlotSlide Vertical','slotfade-vertical' => 'SlotFade Vertical', 'curtain-1' => 'Curtain 1','curtain-2'=>'Curtain 2','curtain-3' => 'Curtain 3','slideleft' => 'Slide Left', 'slideright' => 'Slide Right','slideup'=>'Slide Up','slidedown' => 'Slide Down','papercut' => 'Premium - Paper Cut', '3dcurtain-horizontal' => 'Premium - 3D Curtain Horizontal','3dcurtain-vertical' => 'Premium - 3D Curtain Vertical', 'flyin' => 'Premium - Fly In','turnoff' => 'Premium - Turn Off', 'cubic' => 'Premium - Cubic'),
          'disabled' => false,
          'readonly' => false,
          'after_element_html' => '<small>The appearance transition of this slide.</small>',
          'tabindex' => 1
        ));		
		$fieldset->addField('slot_amount', 'text', array(
			'name' 		=> 'slot_amount',
			'label' 	=> $this->__('Slot Amount'),
			'title' 	=> $this->__('Slot Amount'),
			'value'  => '7',
			'after_element_html' => '<small> The number of slots or boxes the slide is divided into. If you use boxfade, over 7 slots can be juggy.</small>'
		));
		$fieldset->addField('transition_rotation', 'text', array(
			'name' 		=> 'transition_rotation',
			'label' 	=> $this->__('Rotation'),
			'title' 	=> $this->__('Rotation'),
			'value'  => '0',
			'after_element_html' => '<small> Rotation (-720 -> 720, 999 = random) Only for Simple Transitions.</small>'
		));
		$fieldset->addField('transition_duration', 'text', array(
			'name' 		=> 'transition_duration',
			'label' 	=> $this->__('Transition Duration'),
			'title' 	=> $this->__('Transition Duration'),
			'value'  => '300',
			'after_element_html' => '<small>The duration of the transition (Default:300, min: 100 max 2000).</small>'
		));
		$fieldset->addField('delay', 'text', array(
			'name' 		=> 'delay',
			'label' 	=> $this->__('Delay'),
			'title' 	=> $this->__('Delay'),
			'value'  => '',
			'after_element_html' => '<small>New delay value for the Slide. If no delay defined per slide, the delay defined via Options ( 9000 ms) will be used.</small>'
		));
		$fieldset->addField('image', 'image', array(
			'name' 		=> 'image',
			'label' 	=> $this->__('Image'),
			'title' 	=> $this->__('Image'),
			'required'	=> true,
			'class'		=> 'required-entry',
		));
		
		$fieldset->addField('sort_order', 'text', array(
			'name' 		=> 'sort_order',
			'label' 	=> $this->__('Sort Order'),
			'title' 	=> $this->__('Sort Order'),
			'class'		=> 'validate-digits',
		));
		
		$fieldset->addField('is_enabled', 'select', array(
			'name' => 'is_enabled',
			'title' => $this->__('Enabled'),
			'label' => $this->__('Enabled'),
			'required' => true,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
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
