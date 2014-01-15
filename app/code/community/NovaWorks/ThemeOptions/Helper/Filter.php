<?php
class NovaWorks_ThemeOptions_Helper_Filter extends Mage_Core_Helper_Abstract
{
	/**
	 * Regular expression patterns for identifying
	 * shortcodes and parameters
	 *
	 */
	const EXPR_SHOTRCODE_OPEN_TAG = '(\[{{shortcode}}[^\]]{0,}\])';
	const EXPR_SHOTRCODE_CLOSE_TAG = '(\[\/{{shortcode}}[^\]]{0,}\])';
//	const EXPR_SHORTCODE_PARAM = '[ ]{1}([^ ]{1,})=["]{0,1}(.*)["]{0,1}[ \]]{1}';
	const EXPR_SHORTCODE_PARAM = '[ ]{1}([^ ]{1,})=["\']{1}(.*)["\']{1}[ \]]{1}';
	
	/**
	 * Extract shortcodes from a string
	 *
	 * @param string $content
	 * @param string $tag
	 * @return false|array
	 */
	protected function _getShortcodesByTag($content, $tag)
	{
		$shortcodes = array();
		if (strpos($content, '[' . $tag) !== false) {
			$hasCloser = strpos($content, '[/' . $tag . ']') !== false;
			$open = str_replace('{{shortcode}}', $tag, self::EXPR_SHOTRCODE_OPEN_TAG);

			if ($hasCloser) {
				$close = str_replace('{{shortcode}}', $tag, self::EXPR_SHOTRCODE_CLOSE_TAG);

				if (preg_match_all('/' . $open . '(.*)' . $close . '/iU', $content, $matches)) {
					foreach($matches[0] as $matchId => $match) {
						$shortcodes[] = new Varien_Object(array(
							'html' => $match,
							'opening_tag' => $matches[1][$matchId],
							'inner_content' => $matches[2][$matchId],
							'closing_tag' => $matches[3][$matchId],
							'params' => new Varien_Object(NovaWorks_ThemeOptions_Helper_Filter::_parseShortcodeParameters($matches[1][$matchId])),
						));
					}
				}
			}
			else if (preg_match_all('/' . $open . '/iU', $content, $matches)) {
				foreach($matches[0] as $matchId => $match) {
					$shortcodes[] = new Varien_Object(array(
						'html' => $match,
						'opening_tag' => $matches[1][$matchId],
						'params' => new Varien_Object(NovaWorks_ThemeOptions_Helper_Filter::_parseShortcodeParameters($matches[1][$matchId])),
					));
				}
			}
		}

		if (count($shortcodes) > 0) {
			return $shortcodes;
		}
		
		return false;
	}

	/**
	 * Extract parameters from a shortcode opening tag
	 *
	 * @param string $openingTag
	 * @return array
	 */
	protected function _parseShortcodeParameters($openingTag)
	{
		$openingTag = preg_replace('/(\]){1}$/', ' ]', trim($openingTag));
		$openingTag = str_replace(' ', '  ', $openingTag);
		
		$parameters = array();

		if (preg_match_all('/' . self::EXPR_SHORTCODE_PARAM . '/iU', $openingTag, $matches)) {
			foreach($matches[1] as $matchId => $key) {
				$parameters[trim($key)] = trim($matches[2][$matchId], '"\' ');
			}
		}
			
		return $parameters;
	}
	
	/**
	 * Applies a set of filters to the given string
	 *
	 * @param string $content
	 * @param array $params
	 * @return string
	 */
	public function applyFilters($content, array $params = array())
	{
		if (isset($params['object'])) {
			$content = trim(preg_replace('/(&nbsp;)$/', '', trim($content)));
			
			$contentObj = new Varien_Object(array('content' => $content));

			Mage::dispatchEvent('nova_string_filter_before', array('content' => $contentObj, 'object' => $params['object'], 'params' => $params, 'helper' => $this));
			//print_r($this);
			$content = $contentObj->getContent();

			NovaWorks_ThemeOptions_Helper_Filter::_applyShortcodes($content, $params);
			
			$contentObj = new Varien_Object(array('content' => $content));
					
			Mage::dispatchEvent('nova_string_filter_after', array('content' => $contentObj, 'object' => $params['object'], 'params' => $params, 'helper' => $this));
			
			$content = $contentObj->getContent();
		}
		return $content;
	}
	
	/**
	 * Add paragraph tags to the content
	 * Taken from the WordPress core
	 * Long live open source!
	 *
	 * @param string &$content
	 */
	protected function _addParagraphsToString(&$content)
	{
		$pee = $content;

		$pee = str_replace(array('<p>[', ']</p>'), array('[', ']'), $pee);

		$content = $pee;
	}

	/**
	 * Preserve new lines
	 * Used as callback in _addParagraphsToString
	 *
	 * @param array $matches
	 * @return string
	 */
	public function _preserveNewLines($matches)
	{
		return str_replace("\n", "<WPPreserveNewline />", $matches[0]);
	}

	/**
	 * Apply shortcodes to the content
	 *
	 * @param string &$content
	 * @param array $params = array
	 */
	protected function _applyShortcodes(&$content, $params = array())
	{
		$content = str_replace("\r\n","",$content);
		NovaWorks_ThemeOptions_Helper_Filter::_addParagraphsToString($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_pre_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_icon_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_full_conlumn_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_full_one_two_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_full_one_three_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_full_one_four_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_full_two_third_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_full_three_third_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_full_collections_shortcode($content, $params);
		
		NovaWorks_ThemeOptions_Helper_Filter::add_cool_box_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_container_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_products_list_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_banner_simple_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_collection_item_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_full_brands_list_shortcode($content, $params);
		NovaWorks_ThemeOptions_Helper_Filter::add_brand_item_shortcode($content, $params);

		$contentObj = new Varien_Object(array('content' => $content));	
		Mage::dispatchEvent('nova_shortcode_apply', array('content' => $contentObj, 'object' => $params['object'], 'params' => $params, 'helper' => $this));
		
		$content = $contentObj->getContent();
	}
	protected function add_pre_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[pre') === false) {
			return $this;
		}
		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'pre')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$content_1 = str_replace("[","&#91;",$shortcode->getInnerContent());
				$content_2 = str_replace("]","&#93;",$content_1);
				$html = array(
					'<pre>',
					$content_2,
					'</pre>'
				);
				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}
	/**
	 * Apply the caption short code
	 *
	 * @param string &$content
	 * @param array $params = array
	 */
	protected function add_container_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[container') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'container')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					sprintf('<div class="nova-container-shortcode row-fluid %s">',$params->getClass()),
					$shortcode->getInnerContent(),
					'</div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}
	protected function add_cool_box_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[cool_box') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'cool_box')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				if($params->getBg_color()){
					$bg_color = "background-color:".$params->getBg_color().";";
				}
				if($params->getBg_image()){
					$bg_image = "background-image: url('".$params->getBg_image()."');";
				}
				if($params->getBg_repeat()){
					$bg_repeat = "background-repeat:".$params->getBg_repeat().";";
				}else{
					$bg_repeat = "background-repeat:no-repeat;";
				}
				if($bg_image){
					$bg_re = $bg_repeat;
				}
				if($params->getV_space()){
					$v_space = "padding-top:".$params->getV_space()."; padding-bottom:".$params->getV_space().";";
				}
				if($params->getH_space()){
					$h_space = "padding-left:".$params->getH_space()."; padding-right:".$params->getH_space().";";
				}
				$html = array(
					sprintf('<div class="nova-cool-box-shortcode row-fluid %s" style="%s">',$params->getClass(),$bg_color.$bg_image.$bg_re.$v_space.$h_space),
					$shortcode->getInnerContent(),
					'</div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}		
	protected function add_full_conlumn_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[full_column') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'full_column')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					'<div class="span12">',
					$shortcode->getInnerContent(),
					'</div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}		
	protected function add_full_one_two_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[one_half') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'one_half')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					'<div class="span6">',
					$shortcode->getInnerContent(),
					'</div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}
	protected function add_full_one_three_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[one_third') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'one_third')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					'<div class="span4">',
					$shortcode->getInnerContent(),
					'</div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}
	protected function add_full_one_four_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[one_fourth') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'one_fourth')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					'<div class="span3">',
					$shortcode->getInnerContent(),
					'</div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}
	protected function add_full_two_third_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[two_third') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'two_third')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					'<div class="span8">',
					$shortcode->getInnerContent(),
					'</div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}
	protected function add_full_three_third_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[three_fourth') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'three_fourth')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					'<div class="span9">',
					$shortcode->getInnerContent(),
					'</div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}
	protected function add_full_collections_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[collections') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'collections')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					sprintf('<div class="extra-collections"><h2>%s</h2><ul>', $params->getTitle()),
					$shortcode->getInnerContent(),
					'</ul></div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}
	protected function add_full_collections2_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[collections_2') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'collections_2')) !== false) {
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					sprintf('<div class="extra-collections"><h2>%s</h2><ul>', $params->getTitle()),
					$shortcode->getInnerContent(),
					'</ul></div>'
				);

				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
			}
		}
	}
	protected function add_collection_item_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[collection_item') === false) {
			return $this;
		}

		$shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'collection_item');
			$i = 1;

			foreach($shortcodes as $shortcode) {
				 $params = $shortcode->getParams();
				 $html = '
				<li>
   <a href="'.$params->getLink().'">
    <img src="'.$params->getImage().'" />
   </a>
   <div class="hover-disconver">
    <h2 class="title">'.$params->getTitle().'</h2>
    <p class="content">
    '.$params->getContent().'
    </p>
    <a class="view-more" href="'.$params->getLink().'">'.$params->getTextLink().'</a>
   </div>
  </li>

			';
			$content = str_replace($shortcode->getHtml(),$html, $content);
				$i++;
			}
		
	}
	protected function add_full_brands_list_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[brands_list') === false) {
			return $this;
		}

		if (($shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'brands_list')) !== false) {
			$i = 1;
			foreach($shortcodes as $shortcode) {
				$params = $shortcode->getParams();
				$html = array(
					sprintf('
 <div class="brand-slider">
        <div class="row-fluid">
   <h2 class="title">%s</h2>
        </div>
  <div class="brand-list" id="brand-list-%d">
   <ul class="slides">
', $params->getTitle(),$i),
					$shortcode->getInnerContent(),
					'</ul></div></div>
<script type="text/javascript">
  jQuery(window).load(function() {
   jQuery(\'#brand-list-'.$i.'\').flexslider({
   namespace: "nova-slider-",
   animation: "slide",
   easing: "easeInQuart",
   animationLoop: false,
   slideshow: false,
   animationSpeed: 400,      
   pauseOnHover: true,
   controlNav: false,
   itemWidth: 148,
   itemMargin: 12,
   prevText: "<div><i class=\"icon-left-open-big\"></i></div>",           //String: Set the text for the "previous" directionNav item
   nextText: "<div><i class=\"icon-right-open-big\"></i></div>",  
   itemMargin: 0
    });
  });
</script>
'
				);
				
				$content = str_replace($shortcode->getHtml(), implode('', $html), $content);
				$i++;
			}
		}
	}
	protected function add_brand_item_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[brand_item') === false) {
			return $this;
		}

		$shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'brand_item');
			foreach($shortcodes as $shortcode) {
				 $params = $shortcode->getParams();
				 $html = '<li><a href="'.$params->getLink().'"><img src="'.$params->getImage().'" /></a></li>';
				 $content = str_replace($shortcode->getHtml(),$html, $content);
			}
		
	}

	protected function add_banner_simple_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[banner_simple') === false) {
			return $this;
		}

		$shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'banner_simple');
			$i = 1;
			foreach($shortcodes as $shortcode) {
				 $params = $shortcode->getParams();
				 $html = '
				<!---- Simple Banner ---->
			<div class="simple-banner-shortcode" id="simple-banner-'.$i.'">
			 <div class="row-fluid simple-banner-info">
			  <div class="span8 simple-banner-left">
			   <h2 class="title-info">'.$params->getTitle().'</h2>
			   <p>'.$params->getSubtitle().'</p>
			  </div>
			  <div class="span4 simple-button">
			   <a href="'.$params->getButton_link().'">
			    <span>'.$params->getButton_title().'</span>
			   </a>
			  </div>  
			 </div>
			 <div class="clear"></div>
			</div>
			<!---- End Simple Banner ---->
			';
			$content = str_replace($shortcode->getHtml(),$html, $content);
				$i++;
			}
		
	}
	protected function add_products_list_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[products_list') === false) {
			return $this;
		}

		$parts = NovaWorks_ThemeOptions_Helper_Filter::_explode('products_list', $content, true);
		$content = '';
		$i = 0;
		foreach($parts as $part) {
			if ($part['is_opening_tag']) {
				$output =  NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'output', 'product-slider');
				$type	=  NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'type', 'new');
				$blockParams = array(
					'template'			=> NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'template', 'novaworks/custom/'.$output.'.phtml'),
					'title'				=> NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'title', 'Featured Products'),
					'cat'				=> NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'cat', ''),
					'cat_selected'		=> NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'cat_selected', ''),
					'count'				=> NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'count', ''),
					'button_title'		=> NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'button_title', ''),
					'button_link'		=> NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'button_link', ''),
					'id'				=> NovaWorks_ThemeOptions_Helper_Filter::_getMatchedString($part['content'], 'id', 'product-flexslider-'.$i),
				);
				
				$part['content'] = NovaWorks_ThemeOptions_Helper_Filter::_generateBlockTag('custom/'.$type, $blockParams);
			}

			$content .= $part['content'];
			$i++;
		}
	}	
	
	protected function add_icon_shortcode(&$content, $params = array())
	{
		if (strpos($content, '[icon') === false) {
			return $this;
		}

		$shortcodes = NovaWorks_ThemeOptions_Helper_Filter::_getShortcodesByTag($content, 'icon');
			foreach($shortcodes as $shortcode) {
				 $params = $shortcode->getParams();
				 if($params->getSize()){
					 $size = "font-size:".$params->getSize().";";
				 }
				 if($params->getColor()){
					 $color = "color:".$params->getColor().";";
				 }
				 $html = sprintf('<i class="%s" style="%s"></i>',$params->getName(),$size.$color);
				 $content = str_replace($shortcode->getHtml(),$html, $content);
			}
		
	}
	/**
	 * Generate a block tag for Magento to process
	 *
	 * @param string $type
	 * @param $blockparams = array()
	 * @param string $name = null
	 * @return string
	 */
	protected function _generateBlockTag($type, array $blockParams = array(), $name = null)
	{
		if (isset($blockParams['type'])) {
			unset($blockParams['type']);
		}
		
		if (!$name) {
			$name = 'nova_block_' . rand(1, 9999);
		}
		
		$blockParams['name'] 	= $name;
		$blockParams 				= array_merge(array('type' => $type), $blockParams);
		
		foreach($blockParams as $key => $value) {
			if ($value) {
				$blockParams[$key] = sprintf('%s="%s"', $key, $value);
			}
			else {
				unset($blockParams[$key]);
			}
		}	
		
		return sprintf('{{block %s}}', implode(' ', $blockParams));
	}
	
	/**
	 * Explodes a string into parts based on the given short tag
	 *
	 * @param string $shortcode
	 * @param string $content
	 * @param bool $splitTags = false
	 * @return array
	 */
	protected function _explode($shortcode, $content, $splitTags = false)
	{
		$pattern 	= $splitTags ? "/(\[" . $shortcode . "[^\]]*\])|(\[\/".$shortcode . "\])/" : "/(\[" . $shortcode . "[^\]]*\].*?\[\/".$shortcode . "\])/";
		$parts 		= preg_split($pattern, $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		
		return NovaWorks_ThemeOptions_Helper_Filter::_sortExplodedString($parts, $shortcode);
	}

	/**
	 * Sorts and classifies a string exploded by self::_explode
	 *
	 * @param array $parts
	 * @param string $shortcode
	 * @return array
	 */
	protected function _sortExplodedString(array $parts, $shortcode)
	{
		foreach($parts as $key => $part) {
			if (strpos($part, "[$shortcode") !== false) {
				$parts[$key] = array('is_opening_tag' => true, 'is_closing_tag' => false,  'content' => $part);
			}
			else if (strpos($part, "[/$shortcode]")  !== false) {
				$parts[$key] = array('is_opening_tag' => false, 'is_closing_tag' => true,  'content' => $part);
			}
			else {
				$parts[$key] = array('is_opening_tag' => false, 'is_closing_tag' => false, 'content' => $part);
			}
		}

		return $parts;
	}

	/**
	 * Returns a matched string from $buffer
	 *
	 * @param string $buffer
	 * @param string $field
	 * @return string
	 */
	protected function _getMatchedString($buffer, $field, $defaultValue = null)
	{
		return ($matchedValue = NovaWorks_ThemeOptions_Helper_Filter::_match("/".$field."=['\"]([^'\"]+)['\"]/", $buffer, 1)) ? $matchedValue : $defaultValue;
	}

	/**
	 * Wrapper for preg_match that adds extra functionality
	 *
	 * @param string $pattern
	 * @param string $value
	 * @param int $keyToReturn
	 * @return mixed
	 */
	public function _match($pattern, $value, $keyToReturn = -1)
	{
		$result = array();
		preg_match($pattern, $value, $result);
		
		if ($keyToReturn == -1) {
			return $result;
		}

		return isset($result[$keyToReturn]) ? $result[$keyToReturn] : null;
	}
}
