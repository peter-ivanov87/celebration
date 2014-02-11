<?php
/**
 * Mageplace Flash Magazine
 *
 * @category    Mageplace
 * @package     Mageplace_Flashmagazine
 * @copyright   Copyright (c) 2010 Mageplace. (http://www.mageplace.com)
 * @license     http://www.mageplace.com/disclaimer.html
 */
 
class Mageplace_Flashmagazine_Helper_Community extends Mage_Core_Helper_Abstract
{
	const FLASHMAGAZINE_URL_NAME = 'flashmagazine';

	protected $_config = array();
	protected $_isDir = 0;

	/**
	 *  Return config var
	 *
	 *  @param	string $key Var path key
	 *  @param	int $storeId Store View Id
	 *  @return	mixed
	 */
	public function getConfig($key, $section)
	{
		if(is_null($section) || is_null($key)) {
			return null;
		}

		$section = strtolower($section);
		$key = strtolower($key);
		if(empty($this->_config[$this->_isDir][$section][$key])) {
			$value = $this->_getConfigValue($key, $section);
			$this->_config[$section][$key] = $value;
		}

		return @$this->_config[$section][$key];
	}

	protected function _getConfigValue($key, $section)
	{
		$value = Mage::getStoreConfig('flashmagazine/' . $section . '/' . $key);

		if(preg_match_all('/\{\{([^\}]*)\}\}/i', $value, $dirs) && !empty($dirs[1])) {
			if($this->_isDir) {
				$mage_dirs = Mage::getConfig()->getOptions()->getData();
			}

			foreach($dirs[1] as $key=>$dir) {
				if($this->_isDir) {
					if(array_key_exists($dir, $mage_dirs)) {
						$value = str_replace($dirs[0][$key], $mage_dirs[$dir], $value);
					} else {
						$node_name = str_replace('_dir', '', $dir);
						$value = str_replace($dirs[0][$key], $this->_getConfigValue($node_name, $section), $value);
					}

				} else {
					$node_name = str_replace('_dir', '', $dir);

					try {
						$path = Mage::getBaseUrl($node_name);
						$path = preg_replace('/\/$/', '', $path);
						$value = str_replace($dirs[0][$key], $path, $value);
					} catch(Exception $e) {
						$value = str_replace($dirs[0][$key], $this->_getConfigValue($node_name, $section), $value);
					}
				}
			}
		}

		return $value;
	}

	public function getDir($key)
	{
		$this->_isDir = 1;

		return $this->getConfig($key, 'filesystem');
	}

	public function getPathUrl($key)
	{
		$this->_isDir = 0;

		return  $this->getConfig($key, 'filesystem');
	}

	public function getFlashMagazineUrl()
	{
		return Mage::getUrl(self::FLASHMAGAZINE_URL_NAME);
	}

	public function getMagazineUrl($magazine, $action=null, $params=array())
	{
		if (is_int($magazine)) {
			$magazine = Mage::getModel('flashmagazine/magazine')->load($magazine);
		}

		if(is_null($action)) {
			$action = $magazine->getMagazinePopup() ? 'popup' : 'view';
		}

		$params['id'] = $magazine->getId();

		return Mage::getUrl(self::FLASHMAGAZINE_URL_NAME.'/magazine/'.$action, $params);
	}

	/**
	 * Check if a magazine can be shown
	 *
	 * @param  Mageplace_Flashmagazine_Model_Magazine|int $magazine
	 * @return boolean
	 */
	public function canShowMagazine($magazine)
	{
		if (is_int($magazine)) {
			$magazine = Mage::getModel('flashmagazine/magazine')->load($magazine);
		}

		if(!($magazine instanceof Mageplace_Flashmagazine_Model_Magazine)) {
			return false;
		}

		if (!$magazine->getId()) {
			return false;
		}

		if (!$magazine->getIsActive()) {
			return false;
		}

		return true;
	}

	protected function _getHtmlTranslationTable()
	{
		$trans = get_html_translation_table(HTML_ENTITIES);

		$trans[chr(32)]  = '&nbsp;';	// Non-breaking space
		$trans[chr(130)] = '&sbquo;';	// Single Low-9 Quotation Mark
		$trans[chr(131)] = '&fnof;';	// Latin Small Letter F With Hook
		$trans[chr(132)] = '&bdquo;';	// Double Low-9 Quotation Mark
		$trans[chr(133)] = '&hellip;';	// Horizontal Ellipsis
		$trans[chr(134)] = '&dagger;';	// Dagger
		$trans[chr(135)] = '&Dagger;';	// Double Dagger
		$trans[chr(136)] = '&circ;';	// Modifier Letter Circumflex Accent
		$trans[chr(137)] = '&permil;';	// Per Mille Sign
		$trans[chr(138)] = '&Scaron;';	// Latin Capital Letter S With Caron
		$trans[chr(139)] = '&lsaquo;';	// Single Left-Pointing Angle Quotation Mark
		$trans[chr(140)] = '&OElig;';	// Latin Capital Ligature OE
		$trans[chr(145)] = '&lsquo;';	// Left Single Quotation Mark
		$trans[chr(146)] = '&rsquo;';	// Right Single Quotation Mark
		$trans[chr(147)] = '&ldquo;';	// Left Double Quotation Mark
		$trans[chr(148)] = '&rdquo;';	// Right Double Quotation Mark
		$trans[chr(149)] = '&bull;';	// Bullet
		$trans[chr(150)] = '&ndash;';	// En Dash
		$trans[chr(151)] = '&mdash;';	// Em Dash
		$trans[chr(152)] = '&tilde;';	// Small Tilde
		$trans[chr(153)] = '&trade;';	// Trade Mark Sign
		$trans[chr(154)] = '&scaron;';	// Latin Small Letter S With Caron
		$trans[chr(155)] = '&rsaquo;';	// Single Right-Pointing Angle Quotation Mark
		$trans[chr(156)] = '&oelig;';	// Latin Small Ligature OE
		$trans[chr(159)] = '&Yuml;';	// Latin Capital Letter Y With Diaeresis

		return $trans;
	}

	public function decodeHTML($string)
	{
		$string = strtr( $string, array_flip($this->_getHtmlTranslationTable( )));
		$string = preg_replace( "/&#([0-9]+);/me", "chr('\\1')", $string );

		return $string;
	}

	public function cleanText($text)
	{
		$text = strip_tags($text);
		$text = str_replace("\t", '', $text);
		$text = trim($this->decodeHTML($text));

		return $text;
	}


	public function editHtmlText($str)
	{
		$str = str_replace('size="1"', 'size="10"', $str);
		$str = str_replace('size="2"', 'size="13"', $str);
		$str = str_replace('size="3"', 'size="16"', $str);
		$str = str_replace('size="4"', 'size="18"', $str);
		$str = str_replace('size="5"', 'size="24"', $str);
		$str = str_replace('size="6"', 'size="32"', $str);
		$str = str_replace('size="7"', 'size="46"', $str);

		$str = str_replace("<div", "<p", $str);
		$str = str_replace("</div>", "</p>", $str);
		$str = str_replace("<strong>", "<b>", $str);
		$str = str_replace("</strong>", "</b>", $str);
		$str = str_replace("<em>", "<i>", $str);
		$str = str_replace("</em>", "</i>", $str);

		// cut out all <p> tags from <li> tags
		$regex = "/<li(.*?)\>(.*?)\<\/li\>/s";
		$search = array ("'<p[^>]*?>'si", "'</p>'si");
		$replace = array ("", "");

		preg_match_all($regex, $str, $matches);
		foreach($matches[2] as $m){
			$text = preg_replace($search, $replace, $m);
			$str = str_replace($m, $text, $str);
		}

		return $str;
	}

	public function htmlEntityDecode($string)
	{
		$string = preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/', 'Mageplace_Flashmagazine_Helper_Data_Convert_Entity', $string);

		return $this->htmlEntityDecodeUtf8($string);
	}

	public function htmlEntityDecodeUtf8($string)
	{
		static $trans_tbl=null;

		// replace numeric entities
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'Mageplace_Flashmagazine_Helper_Data::code2utf(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'Mageplace_Flashmagazine_Helper_Data::code2utf(\\1)', $string);

		// replace literal entities
		if (is_null($trans_tbl)) {
			$trans_tbl = array();

			foreach(get_html_translation_table(HTML_ENTITIES) as $val=>$key) {
				$trans_tbl[$key] = utf8_encode($val);
			}
		}

		return strtr($string, $trans_tbl);
	}

	/**
	 * Returns the utf string corresponding to the unicode value (from php.net, courtesy - romans@void.lv)
	 */
	public function code2utf($num)
	{
		if ($num < 128) return chr($num);
		if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num & 63) + 128);
		if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);
		if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12) & 63) + 128) . chr((($num >> 6) & 63) + 128) . chr(($num & 63) + 128);

		return '';
	}

	/**
	 * Swap HTML named entities with numeric entities
	 * This contains the full HTML 4 Recommendation listing of entities, so the default to discard
	 * entities not in the table is generally good. Pass false to the second argument to return
	 * the faulty entity unmodified, if you're ill or something.
	 */
	public function convertEntity ($matches, $destroy = true)
	{
		static $table = array(
			'quot' => '&#34;','amp' => '&#38;','lt' => '&#60;','gt' => '&#62;','OElig' => '&#338;','oelig' => '&#339;','Scaron' => '&#352;','scaron' => '&#353;','Yuml' => '&#376;','circ' => '&#710;','tilde' => '&#732;','ensp' => '&#8194;','emsp' => '&#8195;','thinsp' => '&#8201;','zwnj' => '&#8204;','zwj' => '&#8205;','lrm' => '&#8206;','rlm' => '&#8207;','ndash' => '&#8211;','mdash' => '&#8212;','lsquo' => '&#8216;','rsquo' => '&#8217;','sbquo' => '&#8218;','ldquo' => '&#8220;','rdquo' => '&#8221;','bdquo' => '&#8222;','dagger' => '&#8224;','Dagger' => '&#8225;','permil' => '&#8240;','lsaquo' => '&#8249;','rsaquo' => '&#8250;','euro' => '&#8364;','fnof' => '&#402;','Alpha' => '&#913;','Beta' => '&#914;','Gamma' => '&#915;','Delta' => '&#916;','Epsilon' => '&#917;','Zeta' => '&#918;','Eta' => '&#919;','Theta' => '&#920;','Iota' => '&#921;','Kappa' => '&#922;','Lambda' => '&#923;','Mu' => '&#924;','Nu' => '&#925;','Xi' => '&#926;','Omicron' => '&#927;','Pi' => '&#928;','Rho' => '&#929;','Sigma' => '&#931;','Tau' => '&#932;','Upsilon' => '&#933;','Phi' => '&#934;','Chi' => '&#935;','Psi' => '&#936;','Omega' => '&#937;','alpha' => '&#945;','beta' => '&#946;','gamma' => '&#947;','delta' => '&#948;','epsilon' => '&#949;','zeta' => '&#950;','eta' => '&#951;','theta' => '&#952;','iota' => '&#953;','kappa' => '&#954;','lambda' => '&#955;','mu' => '&#956;','nu' => '&#957;','xi' => '&#958;','omicron' => '&#959;','pi' => '&#960;','rho' => '&#961;','sigmaf' => '&#962;','sigma' => '&#963;','tau' => '&#964;','upsilon' => '&#965;','phi' => '&#966;','chi' => '&#967;','psi' => '&#968;','omega' => '&#969;','thetasym' => '&#977;','upsih' => '&#978;','piv' => '&#982;','bull' => '&#8226;','hellip' => '&#8230;','prime' => '&#8242;','Prime' => '&#8243;','oline' => '&#8254;','frasl' => '&#8260;','weierp' => '&#8472;','image' => '&#8465;','real' => '&#8476;','trade' => '&#8482;','alefsym' => '&#8501;','larr' => '&#8592;','uarr' => '&#8593;','rarr' => '&#8594;','darr' => '&#8595;','harr' => '&#8596;','crarr' => '&#8629;','lArr' => '&#8656;','uArr' => '&#8657;','rArr' => '&#8658;','dArr' => '&#8659;','hArr' => '&#8660;','forall' => '&#8704;','part' => '&#8706;','exist' => '&#8707;','empty' => '&#8709;','nabla' => '&#8711;','isin' => '&#8712;','notin' => '&#8713;','ni' => '&#8715;','prod' => '&#8719;','sum' => '&#8721;','minus' => '&#8722;','lowast' => '&#8727;','radic' => '&#8730;','prop' => '&#8733;','infin' => '&#8734;','ang' => '&#8736;','and' => '&#8743;','or' => '&#8744;','cap' => '&#8745;','cup' => '&#8746;','int' => '&#8747;','there4' => '&#8756;','sim' => '&#8764;','cong' => '&#8773;','asymp' => '&#8776;','ne' => '&#8800;','equiv' => '&#8801;','le' => '&#8804;','ge' => '&#8805;','sub' => '&#8834;','sup' => '&#8835;','nsub' => '&#8836;','sube' => '&#8838;','supe' => '&#8839;','oplus' => '&#8853;','otimes' => '&#8855;','perp' => '&#8869;','sdot' => '&#8901;','lceil' => '&#8968;','rceil' => '&#8969;','lfloor' => '&#8970;','rfloor' => '&#8971;','lang' => '&#9001;','rang' => '&#9002;','loz' => '&#9674;','spades' => '&#9824;','clubs' => '&#9827;','hearts' => '&#9829;','diams' => '&#9830;','nbsp' => '&#160;','iexcl' => '&#161;','cent' => '&#162;','pound' => '&#163;','curren' => '&#164;','yen' => '&#165;','brvbar' => '&#166;','sect' => '&#167;','uml' => '&#168;','copy' => '&#169;','ordf' => '&#170;','laquo' => '&#171;','not' => '&#172;','shy' => '&#173;','reg' => '&#174;','macr' => '&#175;','deg' => '&#176;','plusmn' => '&#177;','sup2' => '&#178;','sup3' => '&#179;','acute' => '&#180;','micro' => '&#181;','para' => '&#182;','middot' => '&#183;','cedil' => '&#184;','sup1' => '&#185;','ordm' => '&#186;','raquo' => '&#187;','frac14' => '&#188;','frac12' => '&#189;','frac34' => '&#190;','iquest' => '&#191;','Agrave' => '&#192;','Aacute' => '&#193;','Acirc' => '&#194;','Atilde' => '&#195;','Auml' => '&#196;','Aring' => '&#197;','AElig' => '&#198;','Ccedil' => '&#199;','Egrave' => '&#200;','Eacute' => '&#201;','Ecirc' => '&#202;','Euml' => '&#203;','Igrave' => '&#204;','Iacute' => '&#205;','Icirc' => '&#206;','Iuml' => '&#207;','ETH' => '&#208;','Ntilde' => '&#209;','Ograve' => '&#210;','Oacute' => '&#211;','Ocirc' => '&#212;','Otilde' => '&#213;','Ouml' => '&#214;','times' => '&#215;','Oslash' => '&#216;','Ugrave' => '&#217;','Uacute' => '&#218;','Ucirc' => '&#219;','Uuml' => '&#220;','Yacute' => '&#221;','THORN' => '&#222;','szlig' => '&#223;','agrave' => '&#224;','aacute' => '&#225;','acirc' => '&#226;','atilde' => '&#227;','auml' => '&#228;','aring' => '&#229;','aelig' => '&#230;','ccedil' => '&#231;','egrave' => '&#232;','eacute' => '&#233;','ecirc' => '&#234;','euml' => '&#235;','igrave' => '&#236;','iacute' => '&#237;','icirc' => '&#238;','iuml' => '&#239;','eth' => '&#240;','ntilde' => '&#241;','ograve' => '&#242;','oacute' => '&#243;','ocirc' => '&#244;','otilde' => '&#245;','ouml' => '&#246;','divide' => '&#247;','oslash' => '&#248;','ugrave' => '&#249;','uacute' => '&#250;','ucirc' => '&#251;','uuml' => '&#252;','yacute' => '&#253;','thorn' => '&#254;','yuml' => '&#255;'
		);

		if (isset($table[$matches[1]])) return $table[$matches[1]];
		// else
		return $destroy ? '' : $matches[0];
	}
}

function Mageplace_Flashmagazine_Helper_Data_Convert_Entity($matches, $destroy = true)
{
	static $table = array(
		'quot' => '&#34;','amp' => '&#38;','lt' => '&#60;','gt' => '&#62;','OElig' => '&#338;','oelig' => '&#339;','Scaron' => '&#352;','scaron' => '&#353;','Yuml' => '&#376;','circ' => '&#710;','tilde' => '&#732;','ensp' => '&#8194;','emsp' => '&#8195;','thinsp' => '&#8201;','zwnj' => '&#8204;','zwj' => '&#8205;','lrm' => '&#8206;','rlm' => '&#8207;','ndash' => '&#8211;','mdash' => '&#8212;','lsquo' => '&#8216;','rsquo' => '&#8217;','sbquo' => '&#8218;','ldquo' => '&#8220;','rdquo' => '&#8221;','bdquo' => '&#8222;','dagger' => '&#8224;','Dagger' => '&#8225;','permil' => '&#8240;','lsaquo' => '&#8249;','rsaquo' => '&#8250;','euro' => '&#8364;','fnof' => '&#402;','Alpha' => '&#913;','Beta' => '&#914;','Gamma' => '&#915;','Delta' => '&#916;','Epsilon' => '&#917;','Zeta' => '&#918;','Eta' => '&#919;','Theta' => '&#920;','Iota' => '&#921;','Kappa' => '&#922;','Lambda' => '&#923;','Mu' => '&#924;','Nu' => '&#925;','Xi' => '&#926;','Omicron' => '&#927;','Pi' => '&#928;','Rho' => '&#929;','Sigma' => '&#931;','Tau' => '&#932;','Upsilon' => '&#933;','Phi' => '&#934;','Chi' => '&#935;','Psi' => '&#936;','Omega' => '&#937;','alpha' => '&#945;','beta' => '&#946;','gamma' => '&#947;','delta' => '&#948;','epsilon' => '&#949;','zeta' => '&#950;','eta' => '&#951;','theta' => '&#952;','iota' => '&#953;','kappa' => '&#954;','lambda' => '&#955;','mu' => '&#956;','nu' => '&#957;','xi' => '&#958;','omicron' => '&#959;','pi' => '&#960;','rho' => '&#961;','sigmaf' => '&#962;','sigma' => '&#963;','tau' => '&#964;','upsilon' => '&#965;','phi' => '&#966;','chi' => '&#967;','psi' => '&#968;','omega' => '&#969;','thetasym' => '&#977;','upsih' => '&#978;','piv' => '&#982;','bull' => '&#8226;','hellip' => '&#8230;','prime' => '&#8242;','Prime' => '&#8243;','oline' => '&#8254;','frasl' => '&#8260;','weierp' => '&#8472;','image' => '&#8465;','real' => '&#8476;','trade' => '&#8482;','alefsym' => '&#8501;','larr' => '&#8592;','uarr' => '&#8593;','rarr' => '&#8594;','darr' => '&#8595;','harr' => '&#8596;','crarr' => '&#8629;','lArr' => '&#8656;','uArr' => '&#8657;','rArr' => '&#8658;','dArr' => '&#8659;','hArr' => '&#8660;','forall' => '&#8704;','part' => '&#8706;','exist' => '&#8707;','empty' => '&#8709;','nabla' => '&#8711;','isin' => '&#8712;','notin' => '&#8713;','ni' => '&#8715;','prod' => '&#8719;','sum' => '&#8721;','minus' => '&#8722;','lowast' => '&#8727;','radic' => '&#8730;','prop' => '&#8733;','infin' => '&#8734;','ang' => '&#8736;','and' => '&#8743;','or' => '&#8744;','cap' => '&#8745;','cup' => '&#8746;','int' => '&#8747;','there4' => '&#8756;','sim' => '&#8764;','cong' => '&#8773;','asymp' => '&#8776;','ne' => '&#8800;','equiv' => '&#8801;','le' => '&#8804;','ge' => '&#8805;','sub' => '&#8834;','sup' => '&#8835;','nsub' => '&#8836;','sube' => '&#8838;','supe' => '&#8839;','oplus' => '&#8853;','otimes' => '&#8855;','perp' => '&#8869;','sdot' => '&#8901;','lceil' => '&#8968;','rceil' => '&#8969;','lfloor' => '&#8970;','rfloor' => '&#8971;','lang' => '&#9001;','rang' => '&#9002;','loz' => '&#9674;','spades' => '&#9824;','clubs' => '&#9827;','hearts' => '&#9829;','diams' => '&#9830;','nbsp' => '&#160;','iexcl' => '&#161;','cent' => '&#162;','pound' => '&#163;','curren' => '&#164;','yen' => '&#165;','brvbar' => '&#166;','sect' => '&#167;','uml' => '&#168;','copy' => '&#169;','ordf' => '&#170;','laquo' => '&#171;','not' => '&#172;','shy' => '&#173;','reg' => '&#174;','macr' => '&#175;','deg' => '&#176;','plusmn' => '&#177;','sup2' => '&#178;','sup3' => '&#179;','acute' => '&#180;','micro' => '&#181;','para' => '&#182;','middot' => '&#183;','cedil' => '&#184;','sup1' => '&#185;','ordm' => '&#186;','raquo' => '&#187;','frac14' => '&#188;','frac12' => '&#189;','frac34' => '&#190;','iquest' => '&#191;','Agrave' => '&#192;','Aacute' => '&#193;','Acirc' => '&#194;','Atilde' => '&#195;','Auml' => '&#196;','Aring' => '&#197;','AElig' => '&#198;','Ccedil' => '&#199;','Egrave' => '&#200;','Eacute' => '&#201;','Ecirc' => '&#202;','Euml' => '&#203;','Igrave' => '&#204;','Iacute' => '&#205;','Icirc' => '&#206;','Iuml' => '&#207;','ETH' => '&#208;','Ntilde' => '&#209;','Ograve' => '&#210;','Oacute' => '&#211;','Ocirc' => '&#212;','Otilde' => '&#213;','Ouml' => '&#214;','times' => '&#215;','Oslash' => '&#216;','Ugrave' => '&#217;','Uacute' => '&#218;','Ucirc' => '&#219;','Uuml' => '&#220;','Yacute' => '&#221;','THORN' => '&#222;','szlig' => '&#223;','agrave' => '&#224;','aacute' => '&#225;','acirc' => '&#226;','atilde' => '&#227;','auml' => '&#228;','aring' => '&#229;','aelig' => '&#230;','ccedil' => '&#231;','egrave' => '&#232;','eacute' => '&#233;','ecirc' => '&#234;','euml' => '&#235;','igrave' => '&#236;','iacute' => '&#237;','icirc' => '&#238;','iuml' => '&#239;','eth' => '&#240;','ntilde' => '&#241;','ograve' => '&#242;','oacute' => '&#243;','ocirc' => '&#244;','otilde' => '&#245;','ouml' => '&#246;','divide' => '&#247;','oslash' => '&#248;','ugrave' => '&#249;','uacute' => '&#250;','ucirc' => '&#251;','uuml' => '&#252;','yacute' => '&#253;','thorn' => '&#254;','yuml' => '&#255;'
	);

	if (isset($table[$matches[1]])) return $table[$matches[1]];
	// else
	return $destroy ? '' : $matches[0];
}