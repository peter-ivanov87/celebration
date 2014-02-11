<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2010-2011 Amasty (http://www.amasty.com)
 */

require_once 'Amasty/Meta/controllers/Adminhtml/ConfigController.php';

class Amasty_Meta_Adminhtml_CustomController extends Amasty_Meta_Adminhtml_ConfigController
{
	protected $_title = 'Meta Tags Template (Custom URLs)';
	protected $_isCustom = true;
	protected $_blockName = 'custom';
}