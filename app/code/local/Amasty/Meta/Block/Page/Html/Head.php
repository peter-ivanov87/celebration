<?php
class Amasty_Meta_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{
	protected $_rewrittenData = array();

	/**
	 * Set title element text
	 *
	 * @param string $value
	 * @param bool $isMain
	 *
	 * @return $this|Mage_Page_Block_Html_Head
	 */
	public function setTitle($value, $isMain = false)
	{
		$this->_rewriteData('title', $value, $isMain);

		return $this;
	}

	/**
	 * @param array|string $key
	 * @param mixed|null $value
	 * @param bool $isMain
	 *
	 * @return $this|Varien_Object
	 */
	public function setData($key, $value, $isMain = false)
	{
		$this->_rewriteData($key, $value, $isMain);

		return $this;
	}

	/**
	 * @param $dataKey
	 * @param $value
	 * @param $isMain
	 *
	 * @return $this|Varien_Object
	 */
	protected function _rewriteData($dataKey, $value, $isMain)
	{
		if (! in_array($dataKey, $this->_rewrittenData)) {
			if ($isMain) {
				$this->_rewrittenData[] = $dataKey;
			}

			return parent::setData($dataKey, $value);
		}

		return $this;
	}

	/**
	 * @param string $method
	 * @param array $args
	 *
	 * @return $this|mixed|Varien_Object
	 * @throws Varien_Exception
	 */
	public function __call($method, $args)
	{
		switch (substr($method, 0, 3)) {
			case 'set' :
				$key    = $this->_underscore(substr($method, 3));
				$result = $this->setData($key, isset($args[0]) ? $args[0] : null, isset($args[1]) ? $args[1] : false);

				return $result;
			default :
				parent::__call($method, $args);
		}

		throw new Varien_Exception("Invalid method " . get_class($this) . "::" . $method . "(" . print_r($args, 1) .
								   ")");
	}


}
