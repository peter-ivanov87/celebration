<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Multiupload_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

		/* Upload Parameters Fieldset */
		$fieldset_parameters = $form->addFieldset('parameters_fieldset',
			array(
				'legend' => $this->__('Upload Parameters')
			)
		);

		$fieldset_parameters->addField('magazine_id',
			'select',
			array(
				'name'		=> 'magazine_id',
				'label'		=> $this->__('Select a book for the pages'),
				'title'		=> $this->__('Select a book for the pages'),
				'required'	=> true,
				'values'	=> $this->_getMagazinesValuesForForm()
			)
		);

		$fieldset_parameters->addField('page_title',
			'text',
			array(
				'name'		=> 'page_title',
				'label'		=> $this->__('Input general title for pages'),
				'title'		=> $this->__('Input general title for pages'),
				'required'	=> true,
			)
		);

		$fieldset_parameters->addField('source_type',
			'select',
			array(
				'name'		=> 'source_type',
				'label'		=> $this->__('Select multiupload type'),
				'title'		=> $this->__('Select multiupload type'),
				'required'	=> true,
				'onchange'	=> 'setSourceType(this)',
				'options'	=> array (
						'file'	=> $this->__('Upload Package File'),
						'dir'	=> $this->__('Install from Directory'),
				)
			)
		);

		/* Upload Fieldset */
		$fieldset_upload = $form->addFieldset('upload_fieldset',
			array(
				'legend' => $this->__('Upload Package File')
			)
		);

		$fieldset_upload->addField('upload_package',
			'file',
			array(
				'name'		=> 'upload_package',
				'label'		=> $this->__('Package File'),
				'note'		=> $this->__('Select zip files')
			)
		);


		/* Install Fieldset */
		$fieldset_install = $form->addFieldset('install_fieldset',
			array(
				'legend' => $this->__('Install from Directory')
			)
		);

		$fieldset_install->addField('input_dir',
			'text',
			array(
				'name'		=> 'input_dir',
				'label'		=> $this->__('Input Directory'),
				'title'		=> $this->__('Input Directory'),
				'value'		=> 'media/',
				'disabled'	=> true
			)
		);

		$fieldset_install->addField('delete_files',
			'checkbox',
			array(
				'name'		=> 'delete_files',
				'label'		=> $this->__('Delete source files from directory after upload'),
				'title'		=> $this->__('Delete source files from directory after upload'),
				'value'		=> 1,
				'disabled'	=> true
			)
		);

		$form->setUseContainer(true);
		$form->setId('edit_form');
		$form->setMethod('post');
		$form->setEnctype('multipart/form-data');
		$form->setAction($this->getSaveUrl());

		$this->setForm($form);
	}

	/**
	 * Helper function to load books collection
	 */
	protected function _getMagazinesValuesForForm()
	{
		return Mage::getResourceModel('flashmagazine/magazine_collection')->toOptionArray();
	}

	public function getSaveUrl()
	{
		return $this->getUrl('*/*/save');
	}
}
