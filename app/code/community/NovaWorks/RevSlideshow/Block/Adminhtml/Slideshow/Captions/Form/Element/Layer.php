<?php
/**
 * Altima Lookbook Free Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Altima
 * @package    Altima_LookbookFree
 * @author     Altima Web Systems http://altimawebsystems.com/
 * @email      support@altima.net.au
 * @copyright  Copyright (c) 2012 Altima Web Systems (http://altimawebsystems.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class NovaWorks_RevSlideshow_Block_Adminhtml_Slideshow_Captions_Form_Element_Layer extends Varien_Data_Form_Element_Abstract
{
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('hidden');
    }

 public function getElementHtml()
 {
 	$html = '<div id="shell">
	
	<div id="hb-shell">
		<div id="hb-top-wrap" class="hb-main-wrap">
			<div id="hb-global-settings-wrap">
				<h1>Global Settings</h1>
				<table>
					<tr>
						<td width="100">Show tooltips on: </td>
						<td>
							<select id="show-select" autocomplete="off">
								<option value="mouseover" selected>Mouseover</option>
								<option value="click">Click</option>
								<option value="always">Always Visible</option>
							</select>
							<div class="form-help">This option determines how the user will trigger the tooltips - when he clicks on the spot, when he hovers the mouse over it, or the tooltips will be visible all the time. This is not active in the content builder.</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div id="hb-main-wrap" class="hb-main-wrap">
			<div id="hb-settings-wrap">
				<h2>Selected Layer Settings</h2>
				<table>
					<tr>
						<td width="100">Layer visibility: </td>
						<td>
							<select id="visible-select">
								<option value="visible">Visible</option>
								<option value="invisible" selected>Invisible</option>
							</select>
							<div class="form-help">Determines the visibility of the spot. If set to "invisible", the user will not know that there is a spot, unless he triggers it. <br />The spot will not look the same in the final product as it looks in the content builder.</div>
						</td>
					</tr>
					<tr>
						<td width="100">Tooltip width: </td>
						<td>
							<input type="text" id="tooltip-width">
							<!-- <br /> -->
							<input type="checkbox" id="tooltip-auto-width" checked value="Auto"><label for="tooltip-auto-width">Auto</label>
							<div class="form-help">If you need a fixed value for the tooltip set a number in pixels (without "px") in the text field. If you don\'t, then check "Auto".</div>
						</td>
					</tr>
					<tr>
						<td>Popup position: </td>
						<td>
							<select id="position-select">
								<option value="left" selected>Left</option>
								<option value="right">Right</option>
								<option value="top">Top</option>
								<option value="bottom">Bottom</option>
							</select>
							<div class="form-help">Choose where you want the popup to appear, relative to the spot that it belongs to.</div>
						</td>
					</tr>
					<tr>
						<td>Content: </td>
						<td>
							<textarea id="content" autocomplete="off"></textarea>
						</td>
					</tr>
					<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td>Delete?</td>
						<td><input type="button" id="delete" value="Delete Layer"></td>
					</tr>
				</table>
			</div>
			<div id="hb-map-wrap">
				<img src="http://nikolaydyankovdesign.com/wp-content/themes/nikolaydyankovdesign/demos/layerpot-map/builder/images/image3.jpg">
			</div>
			<div class="clear"></div>
		</div>
		<div class="hb-main-wrap" id="submit-wrap">
			<div id="result" class="ndd-button-green-regular">Generate</div>
		</div>		
		<div id="hb-bottom-wrap" class="hb-main-wrap">
			<h1>Live Preview</h1>
			<div id="hb-live-preview"></div>
		</div>
		<div id="hb-bottom-wrap" class="hb-main-wrap">	
			<div class="left">
				<h1>HTML Code</h1>
				<textarea id="hb-html-code" autocomplete="off"></textarea>
			</div>
			
			<div class="right">
				<h1>JavaScript Code</h1>
				<textarea id="hb-javascript-code" autocomplete="off"></textarea>
			</div>
			<div class="clear"></div>			
		</div>
	</div>
	
	
</div>
';
        
        return $html;
 }
}