<?php
class NovaWorks_Htmlcompression_Model_Observer {

    public function Htmlcompression($observer) {

	if (Mage::helper('htmlcompression')->isEnabled()):
		// Fetches the current event
        $event = $observer->getEvent();  
        $controller = $event->getControllerAction();
		$allHtml = $controller->getResponse()->getBody();
		// Trim each line
		$allHtml = preg_replace('/^\\s+|\\s+$/m', '', $allHtml);
		// Remove HTML comments
		$allHtml =  preg_replace_callback(
            '/<!--([\\s\\S]*?)-->/',
			array($this, '_commentCB'),
			$allHtml); 
		// Remove ws around block/undisplayed elements
        $allHtml = preg_replace('/\\s+(<\\/?(?:area|base(?:font)?|blockquote|body'
            .'|caption|center|cite|col(?:group)?|dd|dir|div|dl|dt|fieldset|form'
            .'|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta'
            .'|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)'
            .'|ul)\\b[^>]*>)/i', '$1', $allHtml);
		// Remove ws outside of all elements
        $allHtml = preg_replace_callback(
            '/>([^<]+)</',
			array($this, '_outsideTagCB'),
			$allHtml);
		$controller->getResponse()->setBody($allHtml);
	endif;
    }

	protected function _outsideTagCB($m)
    {
	if (Mage::helper('htmlcompression')->isEnabled()):
        return '>' . preg_replace('/^\\s+|\\s+$/', ' ', $m[1]) . '<';
	endif;
    }

	protected function _commentCB($m)
    {
        if (Mage::helper('htmlcompression')->isEnabled()):
	return (0 === strpos($m[1], '[') || false !== strpos($m[1], '<!['))
            ? $m[0]
            : '';
	endif;
    }

}
