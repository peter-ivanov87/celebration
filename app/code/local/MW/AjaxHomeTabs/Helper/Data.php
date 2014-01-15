<?php

class MW_AjaxHomeTabs_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	const MYNAME = "MW_AjaxHomeTabs";
	
	function disableConfig()
	{					
			Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".self::MYNAME,1);	
			 Mage::getConfig()->reinit();
	}
	
	
}