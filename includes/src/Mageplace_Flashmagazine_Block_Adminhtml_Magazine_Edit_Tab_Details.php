<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Edit_Tab_Details extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Preparation of current form
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Edit_Tab_Details
	 */
	protected function _prepareForm()
	{
		$model = Mage::registry('flashmagazine_magazine');

		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('magazine_details_');

		$fieldset_details = $form->addFieldset('base_fieldset',
			array(
				'legend'	=> $this->__('Book Details'),
				'class'		=> 'fieldset-wide'
			)
		);

		if ($model->getId()) {
			$fieldset_details->addField('magazine_id',
				'hidden',
				array(
					'name' => 'magazine_id'
				)
			);
		}

		$fieldset_details->addField('magazine_title',
			'text',
			array(
				'name'		=> 'magazine_title',
				'label'		=> $this->__('Book Title'),
				'title'		=> $this->__('Book Title'),
				'required'	=> true,
			)
		);

		$fieldset_details->addField('is_active',
			'select',
			array(
				'name'		=> 'is_active',
				'label'		=> $this->__('Book Status'),
				'title'		=> $this->__('Book Status'),
				'required'	=> true,
				'options'	=> array (
					1 => Mage::helper('cms')->__('Enabled'),
					0 => Mage::helper('cms')->__('Disabled')
				)
			)
		);

		$fieldset_details->addField('magazine_sort_order',
			'text',
			array(
				'name'		=> 'magazine_sort_order',
				'label'		=> $this->__('Book Position'),
				'title'		=> $this->__('Book Position'),
				'class'		=> 'validate-digits',
				'style'		=> 'width: 30px !important;',
			)
		);

		if (!Mage::app()->isSingleStoreMode()) {
			$fieldset_details->addField('store_id',
				'multiselect',
				array(
					'name'		=> 'stores[]',
					'label'		=> Mage::helper('cms')->__('Store view'),
					'title'		=> Mage::helper('cms')->__('Store view'),
					'required'	=> true,
					'values'	=> Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
				)
			);
		} else {
			$fieldset_details->addField('store_id',
				'hidden',
				array(
					'name'	=> 'stores[]',
					'value'	=> Mage::app()->getStore(true)->getId()
				)
			);

			$model->setStoreId(Mage::app()->getStore(true)->getId());
		}

		$fieldset_details->addField('magazine_category_id',
			'select',
			array(
				'name'		=> 'magazine_category_id',
				'label'		=> $this->__('Book Category'),
				'title'		=> $this->__('Book Category'),
				'required'	=> true,
				'values'	=> $this->_getCategoriesValuesForForm()
			)
		);

		$fieldset_details->addField('magazine_template_id',
			'select',
			array(
				'name'		=> 'magazine_template_id',
				'label'		=> $this->__('Book Template'),
				'title'		=> $this->__('Book Template'),
				'required'	=> true,
				'values'	=> $this->_getTemplatesValuesForForm()
			)
		);

		$fieldset_details->addField('magazine_resolution_id',
			'select',
			array(
				'name'		=> 'magazine_resolution_id',
				'label'		=> $this->__('Book Resolution'),
				'title'		=> $this->__('Book Resolution'),
				'required'	=> true,
				'values'	=> $this->_getResolutionsValuesForForm()
			)
		);

		$fieldset_details->addField('magazine_imgsub',
			'checkbox',
			array(
				'name'		=> 'magazine_imgsub',
				'label'		=> $this->__("Use subfolder for the book's files"),
				'title'		=> $this->__("Use subfolder for the book's files"),
				'checked'	=> $model->getData('magazine_imgsub'),
			)
		);

		$fieldset_details->addField('magazine_imgsubfolder',
			'text',
			array(
				'name'		=> 'magazine_imgsubfolder',
				'label'		=> $this->__("Subfolder name"),
				'title'		=> $this->__("Subfolder name"),
				'note'		=> $this->__("Please, use only latin letters and numbers"),
				'disabled'	=> !($model->getData('magazine_imgsub')),
			)
		);

		$form->getElement('magazine_imgsub')
			->setOnclick(
				"javascript:document.getElementById('".$form->getHtmlIdPrefix().$form->getElement('magazine_imgsubfolder')->getId()."').disabled = !document.getElementById('".$form->getHtmlIdPrefix().$form->getElement('magazine_imgsubfolder')->getId()."').disabled"
			);


		$fieldset_details->addField('magazine_thumb',
			'image',
			array(
				'name'		=> 'magazine_thumb',
				'label'		=> $this->__('Book thumbnail'),
				'note'		=> $this->__('Select gif, jpg or png files')
			)
		);
		if($model->getData('magazine_thumb')) {
			$model->setData('magazine_thumb', Mage::helper('flashmagazine')->getPathUrl('thumb').'/'.$model->getData('magazine_thumb'));
		}


		$fieldset_details->addField('magazine_popup',
			'select',
			array(
				'name'		=> 'magazine_popup',
				'label'		=> $this->__('Show book mode'),
				'title'		=> $this->__('Show book mode'),
				'required'	=> true,
				'options'	=> array (
					1 => $this->__('Popup window'),
					0 => $this->__('Direct link')
				)
			)
		);

		$fieldset_details->addField('magazine_enable_fullscreen',
			'select',
			array(
				'name'		=> 'magazine_enable_fullscreen',
				'label'		=> $this->__('Enable fullscreen button'),
				'title'		=> $this->__('Enable fullscreen button'),
				'options'	=> array (
					1 => Mage::helper('cms')->__('Enabled'),
					0 => Mage::helper('cms')->__('Disabled')
				)
			)
		);

		$fieldset_details->addField('magazine_enable_print',
			'select',
			array(
				'name'		=> 'magazine_enable_print',
				'label'		=> $this->__('Enable print button'),
				'title'		=> $this->__('Enable print button'),
				'options'	=> array (
					1 => Mage::helper('cms')->__('Enabled'),
					0 => Mage::helper('cms')->__('Disabled')
				)
			)
		);

		/*$fieldset_details->addField('magazine_view_style',
			'select',
			array(
				'name'		=> 'magazine_view_style',
				'label'		=> $this->__('Show 1 or 2 pages'),
				'title'		=> $this->__('Show 1 or 2 pages'),
				'required'	=> true,
				'options'	=> array (
					1 => 1,
					2 => 2
				)
			)
		);*/

		$fieldset_details->addField('magazine_hide_shadow',
			'select',
			array(
				'name'		=> 'magazine_hide_shadow',
				'label'		=> $this->__('Hide shadow when flipping first/last page'),
				'title'		=> $this->__('Hide shadow when flipping first/last page'),
				'options'	=> array (
					0 => Mage::helper('cms')->__('No'),
					1 => Mage::helper('cms')->__('Yes')
				)
			)
		);

		/*$fieldset_details->addField('magazine_list_description',
			'editor',
			array(
				'name'		=> 'magazine_list_description',
				'label'		=> $this->__('Book description'),
				'title'		=> $this->__('Book description'),
				'config'	=> Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
				'note'		=> $this->__("This text will be shown under the book's title in the list of books"),
			)
		);*/

		$fieldset_details->addField('magazine_description',
			'editor',
			array(
				'name'		=> 'magazine_description',
				'label'		=> $this->__('Book description'),
				'title'		=> $this->__('Book description'),
				'config'	=> Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
				'note'		=> $this->__("This text will be shown at the beginning of the book when you open it"),
			)
		);

		$form->setValues($model->getData());

		$this->setForm($form);

		return parent::_prepareForm();
	}

	/**
	 * Helper function to load category collection
	 */
	protected function _getCategoriesValuesForForm()
	{
		return Mage::getResourceModel('flashmagazine/category_collection')->toOptionArray();
	}

	/**
	 * Helper function to load template collection
	 */
	protected function _getTemplatesValuesForForm()
	{
		return Mage::getResourceModel('flashmagazine/template_collection')->toOptionArray();
	}

	/**
	 * Helper function to load resolution collection
	 */
	protected function _getResolutionsValuesForForm()
	{
		return Mage::getResourceModel('flashmagazine/resolution_collection')->toOptionArray();
	}
}
