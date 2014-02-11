<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Widget_Chooser extends Mage_Adminhtml_Block_Widget_Grid
{
	protected $_selectedMagazines = array();

	/**
	 * Block construction, prepare grid params
	 *
	 * @param array $arguments Object data
	 */
	public function __construct($arguments=array())
	{
		parent::__construct($arguments);

		$this->setDefaultSort('magazine_id');
		$this->setDefaultDir('ASC');
		$this->setUseAjax(true);
	}

	/**
	 * Prepare chooser element HTML
	 *
	 * @param Varien_Data_Form_Element_Abstract $element Form Element
	 * @return Varien_Data_Form_Element_Abstract
	 */
	public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$uniqId = Mage::helper('core')->uniqHash($element->getId());
		$sourceUrl = $this->getUrl('*/flashmagazine_widget/chooser', array(
			'uniq_id' => $uniqId,
		));

		$chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
			->setElement($element)
			->setTranslationHelper($this->getTranslationHelper())
			->setConfig($this->getConfig())
			->setFieldsetId($this->getFieldsetId())
			->setSourceUrl($sourceUrl)
			->setUniqId($uniqId);

        if ($element->getValue()) {
            $magazine = Mage::getModel('flashmagazine/magazine')->load((int)$element->getValue());
            if ($magazine->getId()) {
                $chooser->setLabel($magazine->getMagazineTitle());
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
		return $element;
	}

	/**
	 * Grid Row JS Callback
	 *
	 * @return string
	 */
	public function getRowClickCallback()
	{
		$chooserJsObject = $this->getId();
		return '
				function (grid, event) {
					var trElement = Event.findElement(event, "tr");
					var magazineId = trElement.down("td").innerHTML;
					var magazineName = trElement.down("td").next().innerHTML;
					'.$chooserJsObject.'.setElementValue(magazineId.replace(/^\s+|\s+$/g,""));
					'.$chooserJsObject.'.setElementLabel(magazineName);
					'.$chooserJsObject.'.close();
				}
		';
	}

	/**
	 * Preparation of the data that is displayed by the grid.
	 *
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Grid
	 */
	protected function _prepareCollection()
	{
		/* @var $collection Mageplace_Flashmagazine_Model_Mysql4_Magazine_Collection */
		$collection = Mage::getResourceModel('flashmagazine/magazine_collection');
		$this->setCollection($collection);

		parent::_prepareCollection();


		return $this;
	}

	/**
	 * Prepare columns for products grid
	 *
	 * @return Mage_Adminhtml_Block_Widget_Grid
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('magazine_id',
			array(
				'header'					=> $this->__('Book ID'),
				'width'						=> '80px',
				'type'						=> 'text',
				'index'						=> 'magazine_id',
				'filter_condition_callback'	=> array(
					$this,
					'_filterMagazineIdCondition'
				)
			)
		);

		$this->addColumn('magazine_name',
			array(
				'header'	=> $this->__('Book Name'),
				'index'		=> 'magazine_title'
			)
		);

		$this->addColumn('magazine_category_id',
			array(
				'header'					=> $this->__('Book Category'),
				'index'						=> 'magazine_category_id',
				'type'						=> 'options',
				'options'					=> $this->_getCategories(),
				'sortable'					=> false,
				'filter_condition_callback'	=> array(
					$this,
					'_filterCategoryCondition'
				)
			)
		);

		$this->addColumn('is_active',
			array(
				'header'	=> $this->__('Active'),
				'index'		=> 'is_active',
				'type'		=> 'options',
				'width'		=> '70px',
				'options'	=> array(
					0 => Mage::helper('cms')->__('No'),
					1 => Mage::helper('cms')->__('Yes')
				)
			)
		);

		return parent::_prepareColumns();
	}

	/**
	 * Helper function to load categories collection
	 */
	protected function _getCategories()
	{
		return Mage::getResourceModel('flashmagazine/category_collection')->toOptionHash();
	}

	/**
	 * Helper function to add survey id filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterMagazineIdCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addMagazineIdFilter($value);
	}

	/**
	 * Helper function to add category filter condition
	 *
	 * @param Mage_Core_Model_Mysql4_Collection_Abstract $collection Data collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column Column information to be filtered
	 */
	protected function _filterCategoryCondition($collection, $column)
	{
		if(!$value = $column->getFilter()->getValue()) {
			return;
		}

		$this->getCollection()->addCategoryFilter($value);
	}

	/**
	 * Adds additional parameter to URL for loading only magazine grid
	 *
	 * @return string
	 */
	public function getGridUrl()
	{
		return $this->getUrl('*/flashmagazine_widget/chooser',
			array(
				'_current' => true,
				'uniq_id' => $this->getId(),
			)
		);
	}

	/**
	 * Setter
	 *
	 * @param array $selectedMagazines
	 * @return Mageplace_Flashmagazine_Block_Adminhtml_Magazine_Widget_Chooser
	 */
	public function setSelectedMagazines($selectedMagazines)
	{
		$this->_selectedMagazines = $selectedMagazines;

		return $this;
	}

	/**
	 * Getter
	 *
	 * @return array
	 */
	public function getSelectedMagazines()
	{
		if ($selectedMagazines = $this->getRequest()->getParam('selected_magazines', null)) {
			$this->setSelectedMagazines($selectedMagazines);
		}

		return $this->_selectedMagazines;
	}
}
