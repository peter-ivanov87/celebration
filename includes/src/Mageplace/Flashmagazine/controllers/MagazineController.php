<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */

class Mageplace_Flashmagazine_MagazineController extends Mage_Core_Controller_Front_Action
{
	protected $_magazine;

	/**
	 * Initialize requested magazine object
	 *
	 * @return Mageplace_Flashmagazine_Model_Magazine
	 */
	protected function _initMagazine()
	{
		if(!$this->_magazine) {
			$this->_magazine = Mage::registry('flashmagazine_current_magazine');
			if(!$this->_magazine) {
				$magazine_id = (int) $this->getRequest()->getParam('id', false);
				if (!$magazine_id) {
					return false;
				}

				$this->_magazine = Mage::getModel('flashmagazine/magazine')->load($magazine_id);

				if (!Mage::helper('flashmagazine')->canShowMagazine($this->_magazine)) {
					return false;
				}

				if($this->getRequest()->getActionName() == 'popup') {
					$this->_magazine->setData('is_popup_view', true);
				} else {
					$this->_magazine->setData('is_popup_view', false);
				}

				Mage::register('flashmagazine_current_magazine', $this->_magazine);
			}
		}


		return $this->_magazine;
	}

	/**
	 * Displays the current magazine view
	 */
	public function viewAction()
	{
		if ($this->_initMagazine()) {
			$this->loadLayout();
			$this->loadLayout()->renderLayout();
		} else {
            $this->_forward('noRoute');
		}
	}

	public function popupAction()
	{
		$this->viewAction();
	}

	public function configXmlAction()
	{
		if (!$magazine = $this->_initMagazine()) {
			return;
		}

		$this->_setHeader();

		$config_xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';

		$config_xml .= '<FlippingBook>';
		$config_xml .= '<width>'.$magazine->getResolutionWidth().'</width>';
		$config_xml .= '<height>'.$magazine->getResolutionHeight().'</height>';
		$config_xml .= '<scaleContent>true</scaleContent>';
		$config_xml .= '<firstPage>0</firstPage>';
		$config_xml .= '<alwaysOpened>false</alwaysOpened>';
		$config_xml .= '<autoFlip>80</autoFlip>';
		$config_xml .= '<flipOnClick>true</flipOnClick>';
		$config_xml .= '<staticShadowsDepth>2</staticShadowsDepth>';
		$config_xml .= '<dynamicShadowsDepth>2</dynamicShadowsDepth>';
		$config_xml .= '<moveSpeed>2</moveSpeed>';
		$config_xml .= '<closeSpeed>3</closeSpeed>';
		$config_xml .= '<gotoSpeed>3</gotoSpeed>';
		$config_xml .= '<flipSound></flipSound>';
		$config_xml .= '<pageBack>0x'.$magazine->getTemplateBackgroundColor().'</pageBack>';
		$config_xml .= '<loadOnDemand>true</loadOnDemand>';
		$config_xml .= '<cachePages>true</cachePages>';
		$config_xml .= '<cacheSize>10</cacheSize>';
		$config_xml .= '<preloaderType>User Defined</preloaderType>';
		$config_xml .= '<userPreloaderId>FBTextPreloader</userPreloaderId>';

		$config_xml .= '<pages>';

		$page_image_path = Mage::helper('flashmagazine')->getPathUrl('page');
		$page_sound_path = Mage::helper('flashmagazine')->getPathUrl('sound');
		$page_video_path = Mage::helper('flashmagazine')->getPathUrl('video');

		$pages = Mage::getResourceModel('flashmagazine/page_collection');
		/* @var $pages Mageplace_Flashmagazine_Model_Mysql4_Page_Collection */
		$pages->addMagazineFilter($magazine)
			->addIsActiveFilter()
			->setOrderByPosition()
			->getItems();

		foreach($pages as $page) {
			$config_xml .= '<page>';
			$config_xml .= '<pageType>'.strtolower($page->getPageType()).'</pageType>';
			$config_xml .= '<pageTitle><![CDATA['.Mage::helper('flashmagazine')->htmlEntityDecodeUtf8($page->getPageTitle()).']]></pageTitle>';
//			$config_xml .= '<pageSound>'.$page_sound_path.'/'.$page->getPageSound().'</pageSound>';
			if($page->getPageType() == 'Image') {
				$zoomsrc = $page->getPageZoomImage() ? $page_image_path.'/'.$page->getPageZoomImage() : $page_image_path.'/'.$page->getPageImage();
				$config_xml .= '<pageSrc>'.$page_image_path.'/'.$page->getPageImage().'</pageSrc>';
				$config_xml .= '<zoompageSrc>'.$zoomsrc.'</zoompageSrc>';
			} else if($page->getPageType() == 'Video') {
				$config_xml .= '<pageSrc>'.$page_video_path.'/'.$page->getPageVideo().'</pageSrc>';
				$config_xml .= '<video_w>'.$page->getPageVideoWdt().'</video_w>';
				$config_xml .= '<video_h>'.$page->getPageVideoHgt().'</video_h>';
				$config_xml .= '<v_align>'.$page->getPageVAlign().'</v_align>';
				$config_xml .= '<h_align>'.$page->getPageHAlign().'</h_align>';
			} else if($page->getPageType() == 'Text') {
				$text = $page->getPageText();
				$text = str_replace("&amp;", "&", $text);
				$text = Mage::helper('flashmagazine')->htmlEntityDecode($text);
				$text = Mage::helper('flashmagazine')->editHtmlText($text);
				$config_xml .= '<pageSrc><![CDATA['.$text.']]></pageSrc>';
			}
			$config_xml .= '</page>';
		}
		$config_xml .= '</pages>';

		$config_xml .= '</FlippingBook>';

		echo $config_xml;

		exit();
	}

	public function paramXmlAction()
	{
		if (!$magazine = $this->_initMagazine()) {
			return;
		}

		$descr = $magazine->getMagazineDescription();
		$descr = str_replace("&amp;", "&", $descr);
		$descr = Mage::helper('flashmagazine')->htmlEntityDecode($descr);
		$descr = Mage::helper('flashmagazine')->editHtmlText($descr);

		$author_descr = $magazine->getMagazineAuthorDescription();
		$author_descr = str_replace("&amp;", "&", $author_descr);
		$author_descr = Mage::helper('flashmagazine')->htmlEntityDecode($author_descr);
		$author_descr = Mage::helper('flashmagazine')->editHtmlText($author_descr);

		$this->_setHeader();

		$param_xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';

		$param_xml .= '<params>';
		$param_xml .= '<magazineName><![CDATA['.$magazine->getName().']]></magazineName>';
//		$param_xml .= '<pagecount>'.$magazine->getMagazineViewStyle().'</pagecount>';
		$param_xml .= '<frontPage show="'.$magazine->getMagazineEnableFrontpage().'">';
		$param_xml .= '<image>'.Mage::helper('flashmagazine')->getPathUrl('image').'/'.$magazine->getMagazineAuthorImage().'</image>';
		$param_xml .= '<bio><![CDATA['.$author_descr.']]></bio>';
		$param_xml .= '<email>'.$magazine->getMagazineAuthorEmail().'</email>';
		$param_xml .= '</frontPage>';
		$param_xml .= '<enableSound>'.$magazine->getMagazineEnableSound().'</enableSound>';
		$param_xml .= '<printButton>'.$magazine->getMagazineEnablePrint().'</printButton>';
		$param_xml .= '<pdfButton>'.$magazine->getMagazineEnablePdf().'</pdfButton>';
		$param_xml .= '<pdfLink>'.Mage::helper('flashmagazine')->getPathUrl('pdf').'/'.$magazine->getMagazineBackgroundPdf().'</pdfLink>';
		$param_xml .= '<fullScreenButton>'.$magazine->getMagazineEnableFullscreen().'</fullScreenButton>';
		$param_xml .= '<logo>'.Mage::helper('flashmagazine')->getPathUrl('logo').'/'.$magazine->getMagazineAuthorLogo().'</logo>';
		$param_xml .= '<template>';
		$param_xml .= '<colorBackground>0x'.$magazine->getTemplateBackgroundColor().'</colorBackground>';
		$param_xml .= '<colorElements>0x'.$magazine->getTemplateElementsColor().'</colorElements>';
		$param_xml .= '<colorElements2>0x'.$magazine->getTemplateAdditionalColor().'</colorElements2>';
		$param_xml .= '<tplType>'.$magazine->getTemplateTypeId().'</tplType>';
		$param_xml .= '</template>';
		$param_xml .= '<ownSound>'.Mage::helper('flashmagazine')->getPathUrl('sound').'/'.$magazine->getMagazineBackgroundSound().'</ownSound>';
		$param_xml .= '<flipSound>'.Mage::helper('flashmagazine')->getPathUrl('sound').'/'.$magazine->getMagazineFlipSound().'</flipSound>';
		$param_xml .= '<info><![CDATA['.$descr.']]></info>';
		$param_xml .= '<hideShadow>'.$magazine->getMagazineHideShadow().'</hideShadow>';
		$param_xml .= '</params>';

		echo $param_xml;

		die;
	}

	public function langXmlAction()
	{
		if (!$magazine = $this->_initMagazine()) {
			return;
		}

		$this->_setHeader();

		$lang_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';

		$lang_xml .= '<lang>';
		$lang_xml .= '<Contents><![CDATA['.$this->_getLang('Contents').']]></Contents>';
		$lang_xml .= '<PageTitles><![CDATA['.$this->_getLang('Page titles').']]></PageTitles>';
		$lang_xml .= '<Help><![CDATA['.$this->_getLang('Help').']]></Help>';
		$lang_xml .= '<helpPage><![CDATA['.$this->_getLang('Help Page').']]></helpPage>';
		$lang_xml .= '<Thumbnails><![CDATA['.$this->_getLang('Thumbnails').']]></Thumbnails>';
		$lang_xml .= '<mailto><![CDATA['.$this->_getLang('Mail to').']]></mailto>';
		$lang_xml .= '<Print><![CDATA['.$this->_getLang('Print').']]></Print>';
		$lang_xml .= '<ViewTheMagazine><![CDATA['.$this->_getLang('View the book').']]></ViewTheMagazine>';
		$lang_xml .= '<NextPage><![CDATA['.$this->_getLang('Next page').']]></NextPage>';
		$lang_xml .= '<PreviousPage><![CDATA['.$this->_getLang('Previous page').']]></PreviousPage>';
		$lang_xml .= '<StartAutoFlipping><![CDATA['.$this->_getLang('Start auto flipping').']]></StartAutoFlipping>';
		$lang_xml .= '<StopAutoFlipping><![CDATA['.$this->_getLang('Stop auto flipping').']]></StopAutoFlipping>';
		$lang_xml .= '<GoToPage><![CDATA['.$this->_getLang('Go to page').']]></GoToPage>';
		$lang_xml .= '<OnOffSound><![CDATA['.$this->_getLang('On/off sound').']]></OnOffSound>';
		$lang_xml .= '<SoundVolume><![CDATA['.$this->_getLang('Sound volume').']]></SoundVolume>';
		$lang_xml .= '<FlippingSpeed><![CDATA['.$this->_getLang('Flipping speed').']]></FlippingSpeed>';
		$lang_xml .= '<FirstPage><![CDATA['.$this->_getLang('First page').']]></FirstPage>';
		$lang_xml .= '<LastPage><![CDATA['.$this->_getLang('Last page').']]></LastPage>';
		$lang_xml .= '<Speed><![CDATA['.$this->_getLang('Speed').']]></Speed>';
		$lang_xml .= '<Volume><![CDATA['.$this->_getLang('Volume').']]></Volume>';
		$lang_xml .= '<PrintOptions><![CDATA['.$this->_getLang('Print options').']]></PrintOptions>';
		$lang_xml .= '<from><![CDATA['.$this->_getLang('From').']]></from>';
		$lang_xml .= '<to><![CDATA['.$this->_getLang('To').']]></to>';
		$lang_xml .= '<all><![CDATA['.$this->_getLang('All').']]></all>';
		$lang_xml .= '<Ok><![CDATA['.$this->_getLang('Ok').']]></Ok>';
		$lang_xml .= '<Cancel><![CDATA['.$this->_getLang('Cancel').']]></Cancel>';
		$lang_xml .= '<Open><![CDATA['.$this->_getLang('Open').']]></Open>';
		$lang_xml .= '<Save><![CDATA['.$this->_getLang('Save').']]></Save>';
		$lang_xml .= '<ZoomIn><![CDATA['.$this->_getLang('Zoom in').']]></ZoomIn>';
		$lang_xml .= '<viewPDF><![CDATA['.$this->_getLang('View PDF').']]></viewPDF>';
		$lang_xml .= '<PDFfile><![CDATA['.$this->_getLang('PDF file').']]></PDFfile>';
		$lang_xml .= '<PageNumber><![CDATA['.$this->_getLang('Page number').']]></PageNumber>';

		$lang_xml .= '<header_TurningPage><![CDATA['.$this->_getLang('Turning page').']]></header_TurningPage>';
		$lang_xml .= '<text_TurningPage><![CDATA['.$this->_getLang('- use these buttons or click on the page corner').']]></text_TurningPage>';
		$lang_xml .= '<header_FlipPages><![CDATA['.$this->_getLang('Flip pages automatically').']]></header_FlipPages>';
		$lang_xml .= '<text_FlipPages_1><![CDATA['.$this->_getLang('- use this button to begin automatic flipping').']]></text_FlipPages_1>';
		$lang_xml .= '<text_FlipPages_2><![CDATA['.$this->_getLang('- use this button to stop automatic flipping').']]></text_FlipPages_2>';
		$lang_xml .= '<text_FlipPages_3><![CDATA['.$this->_getLang('- use this scroll bar to change speed of flipping.').']]></text_FlipPages_3>';
		$lang_xml .= '<header_GoToPage><![CDATA['.$this->_getLang('Go to page').']]></header_GoToPage>';
		$lang_xml .= '<text_GoToPage_1><![CDATA['.$this->_getLang('- input number and press the arrow').']]></text_GoToPage_1>';
		$lang_xml .= '<text_GoToPage_2><![CDATA['.$this->_getLang('- use these buttons to go to first or last page.').']]></text_GoToPage_2>';
		$lang_xml .= '<text_GoToPage_3><![CDATA['.$this->_getLang('Use menu at the top of window to skip to articles.').']]></text_GoToPage_3>';
		$lang_xml .= '<header_Sound><![CDATA['.$this->_getLang('Sound').']]></header_Sound>';
		$lang_xml .= '<text_Sound_1><![CDATA['.$this->_getLang('- use this button to turn on/off sound.').']]></text_Sound_1>';
		$lang_xml .= '<text_Sound_2><![CDATA['.$this->_getLang('- use this scroll bar to change sound volume.').']]></text_Sound_2>';
		$lang_xml .= '<header_Print><![CDATA['.$this->_getLang('Print').']]></header_Print>';
		$lang_xml .= '<text_Print><![CDATA['.$this->_getLang('- press this button and follow instructions.').']]></text_Print>';
		$lang_xml .= '<header_Zooming><![CDATA['.$this->_getLang('Zooming').']]></header_Zooming>';
		$lang_xml .= '<text_Zooming_1><![CDATA['.$this->_getLang('- use this button to zoom in.').']]></text_Zooming_1>';
		$lang_xml .= '<text_thumbs><![CDATA['.$this->_getLang('- use this button to view page thumbnails').']]></text_thumbs>';
		$lang_xml .= '<header_pdf><![CDATA['.$this->_getLang('PDF version').']]></header_pdf>';
		$lang_xml .= '<text_pdf><![CDATA['.$this->_getLang('- use this button to view PDF version of the book').']]></text_pdf>';
		$lang_xml .= '<BtnFullScreen><![CDATA['.$this->_getLang('Full screen').']]></BtnFullScreen>';
		$lang_xml .= '<BtnNormalScreen><![CDATA['.$this->_getLang('Normal screen').']]></BtnNormalScreen>';
		$lang_xml .= '<header_fullScreen><![CDATA['.$this->_getLang('Full screen').']]></header_fullScreen>';
		$lang_xml .= '<text_fullScreen><![CDATA['.$this->_getLang('- use this button to switch to full screen mode').']]></text_fullScreen>';

		$lang_xml .= '</lang>';

		echo $lang_xml;

		die;
	}

	protected function _getLang($string)
	{
		return Mage::helper('flashmagazine')->htmlEntityDecodeUtf8(stripslashes($this->__($string)));
	}

	protected function _setHeader()
	{
		header ('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
		header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header ('Cache-Control: no-cache, must-revalidate');
		header ('Pragma: no-cache');
		header ('Content-Type: text/xml');
	}
}
