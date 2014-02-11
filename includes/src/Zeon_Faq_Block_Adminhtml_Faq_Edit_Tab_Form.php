<?php
/**
 * Zeon Solutions, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Zeon Solutions License
 * that is bundled with this package in the file LICENSE_ZE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.zeonsolutions.com/license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zeonsolutions.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.zeonsolutions.com for more information.
 *
 * @category    Zeon
 * @package     Zeon_Faq
 * @copyright   Copyright (c) 2012 Zeon Solutions, Inc. All Rights Reserved.(http://www.zeonsolutions.com)
 * @license     http://www.zeonsolutions.com/license/
 */

class Zeon_Faq_Block_Adminhtml_Faq_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    /**
     * Set form id prefix, set values if faq is editing
     *
     * @return Zeon_Faq_Block_Adminhtml_List_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $htmlIdPrefix = 'faq_information_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldsetHtmlClass = 'fieldset-wide';
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array('tab_id' => $this->getTabId()));

        /* @var $model Zeon_Faq_Model_Faq */
        $model = Mage::registry('current_faq');
        $contents = $model->getDescription();

        $fieldset = $form->addFieldset('base_fieldset', array(
                'legend'=>Mage::helper('zeon_faq')->__('Faq information'),
                'class'    => $fieldsetHtmlClass,
        ));

        if ($model->getFaqId()) {
            $fieldset->addField('faq_id', 'hidden', array(
                    'name'    => 'faq_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
                'label'    => Mage::helper('zeon_faq')->__('Title'),
                'name'     => 'title',
                'required' => true,
        ));

        $fieldset->addField('status', 'select', array(
            'label'    => Mage::helper('zeon_faq')->__('Status'),
            'name'     => 'status',
            'required' => 'true',
            'disabled' => (bool)$model->getIsReadonly(),
            'options'  => Mage::getModel('zeon_faq/status')->getAllOptions(),
        ));

        $options[] = array(
            'value'     => '',
            'label'     => '',
        );

        $fieldset->addField('category_id', 'select', array(
            'label'  => Mage::helper('zeon_faq')->__('Category'),
            'name'   => 'category_id',
            'values' => array_merge($options, Mage::getResourceSingleton('zeon_faq/category_collection')
                ->addFieldToFilter('status', 1)
                ->addOrder('title', 'asc')
                ->toOptionArray()
            ),
        ));

        if (!$model->getId()) {
            $model->setData('status', Zeon_Faq_Model_Status::STATUS_ENABLED);
        }
          /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'multiselect', array(
                'name'     => 'store_ids[]',
                'label'    => Mage::helper('zeon_faq')->__('Visible In'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value'    => $model->getStoreIds(),
            ));
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                    'name'    => 'store_ids[]',
                    'value'    => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreIds(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('is_most_frequently', 'select', array(
                'label'     => Mage::helper('zeon_faq')->__('Is Most Frequently Asked Question'),
                'name'      => 'is_most_frequently',
                'disabled'  => (bool)$model->getIsReadonly(),
                'options'   =>
                    array(
                        Zeon_Faq_Model_Status::STATUS_ENABLED  => Mage::helper('zeon_faq')->__('Yes'),
                        Zeon_Faq_Model_Status::STATUS_DISABLED => Mage::helper('zeon_faq')->__('No'),
                    ),
        ));

        $fieldset->addField('sort_order', 'text', array(
                'label'        => Mage::helper('zeon_faq')->__('Sort Order'),
                'name'         => 'sort_order',
        ));

        $fieldset->addField('description', 'editor', array(
            'name'      => 'description',
            'label'     => Mage::helper('zeon_faq')->__('Description'),
            'title'     => Mage::helper('zeon_faq')->__('Description'),
            'style'     => 'height:36em',
            'required'  => true,
            'config'    => $wysiwygConfig,
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('zeon_faq')->__('Faq Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}