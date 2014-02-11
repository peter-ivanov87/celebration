<?php
/**
 * DISCLAIMER
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html  
 */

class Mageplace_Flashmagazine_Block_Form_Element_Colorpicker extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('text');
        $this->setExtType('textfield');
        if (isset($attributes['value'])) {
            $this->setValue($attributes['value']);
        }
    }

    /**
     * Output the input field and assign calendar instance to it.
     * In order to output the date:
     * - the value must be instantiated (Zend_Date)
     * - output format must be set (compatible with Zend_Date)
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('input-text');
		
        $html = sprintf(
            '#<input name="%s" id="%s" value="%s" %s style="width:110px !important;" />',
            $this->getName(), $this->getHtmlId(), $this->_escape($this->getValue()), $this->serialize($this->getHtmlAttributes())
        );

        $html .= sprintf('
            <script type="text/javascript">
            //<![CDATA[
                new Control.ColorPicker("%s");
            //]]>
            </script>',
            $this->getHtmlId()
        );

        $html .= $this->getAfterElementHtml();

        return $html;
    }
}
