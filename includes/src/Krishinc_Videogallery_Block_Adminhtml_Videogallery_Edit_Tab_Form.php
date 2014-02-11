<?php
    class Krishinc_Videogallery_Block_Adminhtml_Videogallery_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    {
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);
            $fieldset = $form->addFieldset('Videogallery_form', array('legend'=>Mage::helper('videogallery')->__('Video information')));     

            $fieldset->addField('videogallery_url', 'text', array(
	            'label' => Mage::helper('videogallery')->__('Youtube Video Url'),
	            'class' => 'required-entry',
	            'required' => true,
				'style'     => 'width:100%;',
	            'name' => 'videogallery_url',
				'after_element_html' => '<p class="note"><span>I.e. : http://www.youtube.com/watch?v=mFBIsCyI0PA</span></p>',
	        ));

			$fieldset->addField('videogallery_category', 'text', array(
	            'label' => Mage::helper('videogallery')->__('Video Category'),
	            'required' => false,
				'style'     => 'width:100%;',
	            'name' => 'videogallery_category',
				'after_element_html' => '<p class="note"><span>I.e. : Yoga</span></p>',
	        ));
            if ( Mage::getSingleton('adminhtml/session')->getVideogalleryData() )
            {
                $form->setValues(Mage::getSingleton('adminhtml/session')->getVideogalleryData());
                Mage::getSingleton('adminhtml/session')->setVideogalleryData(null);
            } elseif ( Mage::registry('videogallery_data') ) {
                $form->setValues(Mage::registry('videogallery_data')->getData());
            }
            return parent::_prepareForm();
        }
        protected function getAllManu()
        {
          $product = Mage::getModel('catalog/product');
          $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                      ->setEntityTypeFilter($product->getResource()->getTypeId())
                      ->addFieldToFilter('attribute_code', 'videogallery'); //can be changed to any attribute
          $attribute = $attributes->getFirstItem()->setEntity($product->getResource());
          $videogallerys = $attribute->getSource()->getAllOptions(false);
         
          return $videogallerys;
        }
        
    }