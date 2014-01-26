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

class Zeon_Faq_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
     /**
     * Set form id prefix, set values if faq is editing
     *
     * @return Zeon_Faq_Block_Adminhtml_Category_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $htmlIdPrefix = 'faq_category_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldsetHtmlClass = 'fieldset-wide';

        $model = Mage::registry('current_category');

        Mage::dispatchEvent('adminhtml_category_edit_tab_form_before_prepare_form', array(
                'model' => $model,
                'form' => $form
        ));

        // add category information fieldset
        $fieldset = $form->addFieldset('base_fieldset', array(
                'legend'=>Mage::helper('zeon_faq')->__('Category information'),
                'class'    => $fieldsetHtmlClass,
        ));

        $fieldset->addField('title', 'text', array(
            'label'        => Mage::helper('zeon_faq')->__('Name'),
            'name'        => 'title',
            'required'  => true,
        ));

        $fieldset->addField('identifier', 'text', array(
            'label'    => Mage::helper('zeon_faq')->__('Identifier'),
            'name'    => 'identifier',
        ));

        $fieldset->addField('status', 'select', array(
                'label'     => Mage::helper('zeon_faq')->__('Status'),
                'name'      => 'status',
                'required'  => true,
                'options'   => Mage::getModel('zeon_faq/status')->getAllOptions(),
        ));

        $fieldset->addField('sort_order', 'text', array(
                'label'        => Mage::helper('zeon_faq')->__('Sort Order'),
                'title'        => Mage::helper('zeon_faq')->__('Sort Order'),
                'name'        => 'sort_order',
        ));

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('zeon_faq')->__('General Information');
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