<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_Adminhtml_Flashmagazine_WidgetController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Chooser Source action
	 */
	public function chooserAction()
	{
		$this->getResponse()->setBody(
			$this->_getMagazineBlock()->toHtml()
		);
	}

	protected function _getMagazineBlock()
	{
		return $this->getLayout()
			->createBlock('flashmagazine/adminhtml_magazine_widget_chooser',
				'',
				array(
					'id' => $this->getRequest()->getParam('uniq_id')
				)
			);
	}
}
