<?php
$storeId = $_GET['storeid'];
require_once('../../../../../app/Mage.php'); //Path to Magento
umask(0);
Mage::app();
function  get_categories($categories) {
    foreach($categories as $category) {
        $cat = Mage::getModel('catalog/category')->load($category->getId());
        $count = $cat->getProductCount();
        $arrb = Mage::getModel('catalog/category')->load($category->getId());
        if($arrb->getData('in_cat_menu_background_color')): $bg_color = 'background-color:'.$arrb->getData('in_cat_menu_background_color').';'; endif;
        if($arrb->getData('in_cat_menu_text_color')): $text_color = 'color:'.$arrb->getData('in_cat_menu_text_color').' !important;'; endif;
        if($arrb->getData('in_cat_menu_hover_bg_color')): $bg_color_hover = 'background-color:'.$arrb->getData('in_cat_menu_hover_bg_color').' !important;'; endif;
        if($arrb->getData('in_cat_menu_text_hover')): $text_color_hover = 'color:'.$arrb->getData('in_cat_menu_text_hover').' !important;'; endif;
        if(Mage::getModel('catalog/category')->load($category->getId())->getData('in_cat_menu_status')) {
        	$array .= 'a.in-category-node-'.$category->getId().' {'.$bg_color.$text_color.'}';
        	$array .= 'a.in-category-node-'.$category->getId().':hover {'.$bg_color_hover.$text_color_hover.'}';
        }
        //print_r(Mage::getModel('catalog/category')->load($category->getId())->getData('novaworks_cat_general_color'));
        if($category->hasChildren()) {
            $children = Mage::getModel('catalog/category')->getCategories($category->getId());
             $array .=  get_categories($children);
            }
    }
    return  $array;
}
/* General Theming */
$heading_font 					= Mage::getStoreConfig('themeoptions_theming/theme_general/heading_font', $storeId);
$heading_color 					= Mage::getStoreConfig('themeoptions_theming/theme_general/heading_color', $storeId);
$text_color		 					= Mage::getStoreConfig('themeoptions_theming/theme_general/text_color', $storeId);
$link_color		 					= Mage::getStoreConfig('themeoptions_theming/theme_general/link_color', $storeId);
$active_color		 				= Mage::getStoreConfig('themeoptions_theming/theme_general/active_color', $storeId);

$media_path							= Mage::getBaseUrl('media') . 'theming/';

$w_bg_color		 					= Mage::getStoreConfig('themeoptions_theming/theme_general/wrapper_background_color', $storeId);
$w_bg_image		 					= Mage::getStoreConfig('themeoptions_theming/theme_general/wrapper_background_image', $storeId);
$w_bg_repeat		 				= Mage::getStoreConfig('themeoptions_theming/theme_general/wrapper_background_repeat', $storeId);
$w_bg_attachment		 		= Mage::getStoreConfig('themeoptions_theming/theme_general/wrapper_background_attachment', $storeId);
$w_bg_position			 		= Mage::getStoreConfig('themeoptions_theming/theme_general/wrapper_background_position', $storeId);

$p_bg_color		 					= Mage::getStoreConfig('themeoptions_theming/theme_general/page_background_color', $storeId);
$p_bg_image		 					= Mage::getStoreConfig('themeoptions_theming/theme_general/page_background_image', $storeId);
$p_bg_repeat		 				= Mage::getStoreConfig('themeoptions_theming/theme_general/page_background_repeat', $storeId);
$p_bg_attachment		 		= Mage::getStoreConfig('themeoptions_theming/theme_general/page_background_attachment', $storeId);
$p_bg_position			 		= Mage::getStoreConfig('themeoptions_theming/theme_general/page_background_position', $storeId);

$hc_bg_color		 					= Mage::getStoreConfig('themeoptions_theming/theme_general/hc_background_color', $storeId);
$hc_bg_image		 					= Mage::getStoreConfig('themeoptions_theming/theme_general/hc_background_image', $storeId);
$hc_bg_repeat		 				= Mage::getStoreConfig('themeoptions_theming/theme_general/hc_background_repeat', $storeId);
$hc_bg_attachment		 		= Mage::getStoreConfig('themeoptions_theming/theme_general/hc_background_attachment', $storeId);
$hc_bg_position			 		= Mage::getStoreConfig('themeoptions_theming/theme_general/hc_background_position', $storeId);

header("Content-type: text/css");
if($heading_font):
	$heading_font_url = str_replace(' ', '+',$heading_font);
?>
@import url(//fonts.googleapis.com/css?family=<?php echo $heading_font_url;?>);
<?php endif;?>
body {
	color:<?php echo $text_color?>;
}
a { color:<?php echo $link_color;?>; text-decoration:none; }
<?php if($heading_font):?>
h1,h2,h3,h4,h5,h6,#wide-menu #nav > li > a, #wide-menu #nav ul.level0 > li > a, #default-menu #nav li.active a, #default-menu #nav li a,.home-gallery-price,.iosSlider .slider .item .text1 span,.block-title strong,.block-viewed .regular-price .price,.regular-price .price,.special-price .price,.product-view .box-up-sell h2,.footer-info .newsletter-box .form-subscribe-header,.shipping-info-content,.footer-info-top .footer-info .about-responsive h4,.footer-info-top .footer-info .contact-us h4,.bottom-menu-column h4  {
	font-family:'<?php echo $heading_font?>',sans-serif;
}
<?php endif;?>
<?php if($heading_color):?>
h1,h2,h3,h4,h5,h6,.block-title strong {
	color:<?php echo $heading_color?>;
}
<?php endif;?>
<?php if($active_color):?>
.top-dropdown li,
.top-dropdown a:hover,
.top-header .welcome-msg,
.block-tags .actions a:hover,
.price-box .price,
.regular-price,
.regular-price .price,
.minimal-price-link .price,
.product-name a:hover,
#wide-menu #nav ul li a:hover,
#default-menu #nav ul li a:hover,
#default-menu #nav ul li.active a,
#default-menu #nav ul li.active ul li a:hover,
.header .mini-cart .shopping-cart .price,

.block-viewed .regular-price .price,
.sorter .sort-by ul li a.active,
.sorter .limiter ul li a.active,
.block-account .block-content li.current,
#wide-menu #nav ul.level0 > li > a,
.block-account .block-content li a:hover,

.main .breadcrumbs .home a {
	color:<?php echo $active_color?>;
}
#default-menu #nav ul li a:hover,.focus {
	color:<?php echo $active_color?> !important;
}
.button,button,
.pager .pages a.next,
.pager .pages a.previous,
#home-gallery-custom span,
.block-tags .block-content .tags-list a:hover,
.products-grid .btn-cart,
.products-list .btn-cart,
.product-tabs a:hover,
.product-tabs li.active a,
.product-tabs li.active a:hover,
.home-gallery a.prev:hover ,
.home-gallery a.next:hover,
#twitter_update_list li .tweet-icon {
 	background-color:<?php echo $active_color?>;
 }
.sorter .view-mode a:hover, 
.sorter .view-mode strong, 
.sorter .view-mode strong {
 	background-color:<?php echo $active_color?>!important;
 } 
 .pager .pages a:hover, 
 .pager .pages .current,
 .header .form-search button.button {
 	border-color:<?php echo $active_color?>;
}
.pager .pages a.next,.sorter .view-mode strong.list {
	border:1px solid <?php echo $active_color?>;
}
.mini-cart,#empty_cart_button,.btn-update,.btn-continue  {
	color:<?php echo $text_color?>;
}
#empty_cart_button:hover,
.btn-update:hover,.btn-continue:hover, 
.top-header .i-top-links .welcome-msg {
	color:<?php echo $active_color?>;
}
#cboxClose:hover,#cboxNext:hover,#cboxPrevious:hover{background-color:<?php echo $active_color?>;}
<?php endif;?>
.wrapper {
    background:<?php echo $w_bg_color?><?php if($w_bg_image):?> url(<?php echo $media_path.$w_bg_image?>) <?php echo $w_bg_repeat?>  <?php echo $w_bg_attachment?> <?php echo $w_bg_position?><?php endif;?>;
}
.page {
    background:<?php echo $p_bg_color?><?php if($p_bg_image):?> url(<?php echo $media_path.$p_bg_image?>) <?php echo $p_bg_repeat?>  <?php echo $p_bg_attachment?> <?php echo $p_bg_position?><?php endif;?>;
}
.content-head {
 background:<?php echo $hc_bg_color?><?php if($hc_bg_image):?> url(<?php echo $media_path.$p_bg_image?>) <?php echo $hc_bg_repeat?>  <?php echo $hc_bg_attachment?> <?php echo $hc_bg_position?><?php endif;?>;
}
<?php

/* Top header */
$top_header_status		 											= intval(Mage::getStoreConfig('themeoptions_theming/top_header_theme/top_header_status', $storeId));
if($top_header_status):
	$top_header_container_bg_color		 				= Mage::getStoreConfig('themeoptions_theming/top_header_theme/container_background_color', $storeId);
	$top_header_container_bg_image		 				= Mage::getStoreConfig('themeoptions_theming/top_header_theme/container_background_image', $storeId);
	$top_header_container_bg_repeat		 				= Mage::getStoreConfig('themeoptions_theming/top_header_theme/container_background_repeat', $storeId);
	$top_header_container_bg_attachment		 		= Mage::getStoreConfig('themeoptions_theming/top_header_theme/container_background_attachment', $storeId);
	$top_header_container_bg_position			 		= Mage::getStoreConfig('themeoptions_theming/top_header_theme/container_background_postition', $storeId);
	
	$top_header_inner_bg_color		 						= Mage::getStoreConfig('themeoptions_theming/top_header_theme/inner_background_color', $storeId);
	$top_header_inner_bg_image		 						= Mage::getStoreConfig('themeoptions_theming/top_header_theme/inner_background_image', $storeId);
	$top_header_inner_bg_repeat		 						= Mage::getStoreConfig('themeoptions_theming/top_header_theme/inner_background_repeat', $storeId);
	$top_header_inner_bg_attachment		 				= Mage::getStoreConfig('themeoptions_theming/top_header_theme/inner_background_attachment', $storeId);
	$top_header_inner_bg_position			 				= Mage::getStoreConfig('themeoptions_theming/top_header_theme/inner_background_postition', $storeId);
	
	$top_header_border_top_size 							= Mage::getStoreConfig('themeoptions_theming/top_header_theme/top_border_size', $storeId);
	$top_header_border_top_color 							= Mage::getStoreConfig('themeoptions_theming/top_header_theme/top_border_color', $storeId);
	$top_header_border_bottom_size 						= Mage::getStoreConfig('themeoptions_theming/top_header_theme/bottom_border_size', $storeId);
	$top_header_border_bottom_color 					= Mage::getStoreConfig('themeoptions_theming/top_header_theme/bottom_border_color', $storeId);
	
	$top_header_text_color		 					= Mage::getStoreConfig('themeoptions_theming/top_header_theme/text_color', $storeId);
	$top_header_link_color		 					= Mage::getStoreConfig('themeoptions_theming/top_header_theme/link_color', $storeId);
	$top_header_active_color		 				= Mage::getStoreConfig('themeoptions_theming/top_header_theme/active_color', $storeId);
	
	$dropbox_background				 					= Mage::getStoreConfig('themeoptions_theming/top_header_theme/dropbox_background', $storeId);
	$dropbox_text_color		 							= Mage::getStoreConfig('themeoptions_theming/top_header_theme/dropbox_text_color', $storeId);
	$dropbox_link_color		 							= Mage::getStoreConfig('themeoptions_theming/top_header_theme/dropbox_link_color', $storeId);
	$dropbox_active_color		 						= Mage::getStoreConfig('themeoptions_theming/top_header_theme/dropbox_link_hover_color', $storeId);	
?>
.top-header-container {
	background:<?php echo $top_header_container_bg_color?><?php if($top_header_container_bg_image):?> url(<?php echo $media_path.$top_header_container_bg_image?>) <?php echo $top_header_container_bg_repeat?>  <?php echo $top_header_container_bg_attachment?> <?php echo $top_header_container_bg_position?><?php endif;?>;
	border-top: <?php echo $top_header_border_top_color;?> solid <?php echo $top_header_border_top_size;?>px;
	border-bottom: <?php echo $top_header_border_bottom_color;?> solid <?php echo $top_header_border_bottom_size;?>px;
}
.top-header {
	background:<?php echo $top_header_inner_bg_color?><?php if($top_header_inner_bg_image):?> url(<?php echo $media_path.$top_header_inner_bg_image?>) <?php echo $top_header_inner_bg_repeat?>  <?php echo $top_header_inner_bg_attachment?> <?php echo $top_header_inner_bg_position?><?php endif;?>;
}
.dropdown p.label {
	color:<?php echo $top_header_text_color?>;
}
.top-header .links a {
	color:<?php echo $top_header_link_color?>;
}
.dropdown p.text,.top-header .welcome-msg,.top-header .links a:hover {
	color:<?php echo $top_header_active_color?>;
}
.top-dropdown ul {
	background:<?php echo $dropbox_background?>;
}
.top-dropdown ul li a {
	color:<?php echo $dropbox_link_color?>;
}
.top-dropdown ul li a:hover,.top-dropdown ul li.selected {
	color:<?php echo $dropbox_active_color?>;
}
.top-header .links li a:hover {
	background-color:<?php echo $top_header_active_color?>;
}
<?php endif;?>
<?php
/* Header */
$header_status												= intval(Mage::getStoreConfig('themeoptions_theming/header_theme/status', $storeId));
if($header_status):
	$header_container_bg_color		 				= Mage::getStoreConfig('themeoptions_theming/header_theme/container_background_color', $storeId);
	$header_container_bg_image		 				= Mage::getStoreConfig('themeoptions_theming/header_theme/container_background_image', $storeId);
	$header_container_bg_repeat		 				= Mage::getStoreConfig('themeoptions_theming/header_theme/container_background_repeat', $storeId);
	$header_container_bg_attachment		 		= Mage::getStoreConfig('themeoptions_theming/header_theme/container_background_attachment', $storeId);
	$header_container_bg_position			 		= Mage::getStoreConfig('themeoptions_theming/header_theme/container_background_postition', $storeId);
	
	$header_inner_bg_color		 						= Mage::getStoreConfig('themeoptions_theming/header_theme/inner_background_color', $storeId);
	$header_inner_bg_image		 						= Mage::getStoreConfig('themeoptions_theming/header_theme/inner_background_image', $storeId);
	$header_inner_bg_repeat		 						= Mage::getStoreConfig('themeoptions_theming/header_theme/inner_background_repeat', $storeId);
	$header_inner_bg_attachment		 				= Mage::getStoreConfig('themeoptions_theming/header_theme/inner_background_attachment', $storeId);
	$header_inner_bg_position			 				= Mage::getStoreConfig('themeoptions_theming/header_theme/inner_background_postition', $storeId);
	
	$header_text_color		 					= Mage::getStoreConfig('themeoptions_theming/header_theme/text_color', $storeId);
	$header_link_color		 					= Mage::getStoreConfig('themeoptions_theming/header_theme/link_color', $storeId);
	$header_active_color		 				= Mage::getStoreConfig('themeoptions_theming/header_theme/active_color', $storeId);

	$header_dropbox_background				 					= Mage::getStoreConfig('themeoptions_theming/header_theme/dropbox_background', $storeId);
	$header_dropbox_text_color		 							= Mage::getStoreConfig('themeoptions_theming/header_theme/dropbox_text_color', $storeId);
	$header_dropbox_link_color		 							= Mage::getStoreConfig('themeoptions_theming/header_theme/dropbox_link_color', $storeId);
	$header_dropbox_active_color		 						= Mage::getStoreConfig('themeoptions_theming/header_theme/dropbox_link_hover_color', $storeId);		
?>
.header-container {
	background:<?php echo $header_container_bg_color?><?php if($header_container_bg_image):?> url(<?php echo $media_path.$header_container_bg_image?>) <?php echo $header_container_bg_repeat?>  <?php echo $header_container_bg_attachment?> <?php echo $header_container_bg_position?><?php endif;?>;
}
.header {
	background:<?php echo $header_inner_bg_color?><?php if($header_inner_bg_image):?> url(<?php echo $media_path.$header_inner_bg_image?>) <?php echo $header_inner_bg_repeat?>  <?php echo $header_inner_bg_attachment?> <?php echo $header_inner_bg_position?><?php endif;?>;
	color:<?php echo $header_text_color?>;
}
a.mini-cart-title {
	color:<?php echo $header_link_color?>;
}
a.mini-cart-title:hover,.shopping-cart .price {
	color:<?php echo $header_active_color?> !important;
}
.header .form-search button.button {
	border-color:<?php echo $header_active_color?>;
}
.header .mini-cart .block-content {
	background:<?php echo $header_dropbox_background?>;
	color:<?php echo $header_dropbox_text_color?>;
}
.header .mini-cart .block-content a {
	color:<?php echo $header_dropbox_link_color?>;
}
.header .mini-cart .block-content a:hover {
	color:<?php echo $header_dropbox_active_color?>;
}
<?php endif;?>
<?php
/* Navigation */
$nav_status		 										= intval(Mage::getStoreConfig('themeoptions_theming/nav_theme/status', $storeId));
if($nav_status):
	$nav_height							 				= Mage::getStoreConfig('themeoptions_theming/nav_theme/navigation_height', $storeId);
	$nav_container_bg_color		 				= Mage::getStoreConfig('themeoptions_theming/nav_theme/container_background_color', $storeId);
	$nav_container_bg_image		 				= Mage::getStoreConfig('themeoptions_theming/nav_theme/container_background_image', $storeId);
	$nav_container_bg_repeat		 				= Mage::getStoreConfig('themeoptions_theming/nav_theme/container_background_repeat', $storeId);
	$nav_container_bg_attachment		 		= Mage::getStoreConfig('themeoptions_theming/nav_theme/container_background_attachment', $storeId);
	$nav_container_bg_position			 		= Mage::getStoreConfig('themeoptions_theming/nav_theme/container_background_postition', $storeId);
	
	$nav_inner_bg_color		 						= Mage::getStoreConfig('themeoptions_theming/nav_theme/inner_background_color', $storeId);
	$nav_inner_bg_image		 						= Mage::getStoreConfig('themeoptions_theming/nav_theme/inner_background_image', $storeId);
	$nav_inner_bg_repeat		 						= Mage::getStoreConfig('themeoptions_theming/nav_theme/inner_background_repeat', $storeId);
	$nav_inner_bg_attachment		 				= Mage::getStoreConfig('themeoptions_theming/nav_theme/inner_background_attachment', $storeId);
	$nav_inner_bg_position			 				= Mage::getStoreConfig('themeoptions_theming/nav_theme/inner_background_postition', $storeId);
	
	$nav_bg_color		 										= Mage::getStoreConfig('themeoptions_theming/nav_theme/nav_bg_color', $storeId);
	
	$nav_header_border_top_size 							= Mage::getStoreConfig('themeoptions_theming/nav_theme/top_border_size', $storeId);
	$nav_header_border_top_color 							= Mage::getStoreConfig('themeoptions_theming/nav_theme/top_border_color', $storeId);
	$nav_header_border_bottom_size 						= Mage::getStoreConfig('themeoptions_theming/nav_theme/bottom_border_size', $storeId);
	$nav_header_border_bottom_color 					= Mage::getStoreConfig('themeoptions_theming/nav_theme/bottom_border_color', $storeId);

	$nav_header_space_top_size 						= Mage::getStoreConfig('themeoptions_theming/nav_theme/top_space_size', $storeId);
	$nav_header_space_bottom_size 					= Mage::getStoreConfig('themeoptions_theming/nav_theme/bottom_space_size', $storeId);
	
	$nav_link_color		 						= Mage::getStoreConfig('themeoptions_theming/nav_theme/link_color', $storeId);
	$nav_hover_color		 					= Mage::getStoreConfig('themeoptions_theming/nav_theme/hover_color', $storeId);
	$nav_hover_bg_color		 				= Mage::getStoreConfig('themeoptions_theming/nav_theme/hover_bg_color', $storeId);
	$nav_active_color		 					= Mage::getStoreConfig('themeoptions_theming/nav_theme/active_color', $storeId);
	$nav_active_bg_color		 			= Mage::getStoreConfig('themeoptions_theming/nav_theme/active_bg_color', $storeId);
?>
.nav-container {
	background:<?php echo $nav_container_bg_color?><?php if($nav_container_bg_image):?> url(<?php echo $media_path.$nav_container_bg_image?>) <?php echo $nav_container_bg_repeat?>  <?php echo $nav_container_bg_attachment?> <?php echo $nav_container_bg_position?><?php endif;?>;
}
#nav {
	background:<?php echo $nav_container_bg_color?><?php if($nav_container_bg_image):?> url(<?php echo $media_path.$nav_container_bg_image?>) <?php echo $nav_container_bg_repeat?>  <?php echo $nav_container_bg_attachment?> <?php echo $nav_container_bg_position?><?php endif;?>;
}
.nav-container:nth-child(n) {
	filter: none;
}
.nav-inner,.mobi-nav {
	background:<?php echo $nav_inner_bg_color?><?php if($nav_inner_bg_image):?> url(<?php echo $media_path.$nav_inner_bg_image?>) <?php echo $nav_inner_bg_repeat?>  <?php echo $nav_inner_bg_attachment?> <?php echo $nav_inner_bg_position?><?php endif;?>;
	padding-top:<?php echo $nav_header_space_top_size;?>;
	padding-bottom:<?php echo $nav_header_space_bottom_size;?>;
}
#nav {
	background:<?php echo $nav_bg_color?>;
 	border-top: <?php echo $nav_header_border_top_color;?> solid <?php echo $nav_header_border_top_size;?>px;
	border-bottom: <?php echo $nav_header_border_bottom_color;?> solid <?php echo $nav_header_border_bottom_size;?>px;	
}
#default-menu #nav li.active a, #default-menu #nav li.active a:hover,#wide-menu #nav > li.active:hover > a,#default-menu #nav li.active a:hover,.mobi-nav h1  {	
    background-color:<?php echo $nav_active_bg_color?>;
    color:<?php echo $nav_active_color?>;
}
.mobi-nav h1 span {color:<?php echo $nav_active_color?>;}
#wide-menu #nav > li > a,#default-menu #nav > li > a {
	color:<?php echo $nav_link_color?>;
}
#wide-menu #nav > li:hover > a,#default-menu #nav li a:hover {
	color:<?php echo $nav_hover_color?>;
	background-color:<?php echo $nav_hover_bg_color?>;
}
#default-menu #nav li.over a,
#default-menu #nav li a:hover {
	color:<?php echo $nav_hover_color?>;
	background-color:<?php echo $nav_hover_bg_color?>;
}
.content-head {min-height: <?php echo $nav_height?>px;}
#default-menu #nav li a,#wide-menu #nav > li > a,.menu-logo {line-height: <?php echo $nav_height?>px;}
#default-menu #nav ul,#default-menu #nav div,#wide-menu #nav ul.level0 {
	top: <?php echo $nav_height?>px;
}
<?php endif;?>
<?php
$content_status		 												= intval(Mage::getStoreConfig('themeoptions_theming/content_theme/status', $storeId));
if($content_status):
	$content_container_bg_color		 					= Mage::getStoreConfig('themeoptions_theming/content_theme/container_background_color', $storeId);
	$content_container_bg_image		 					= Mage::getStoreConfig('themeoptions_theming/content_theme/container_background_image', $storeId);
	$content_container_bg_repeat		 				= Mage::getStoreConfig('themeoptions_theming/content_theme/container_background_repeat', $storeId);
	$content_container_bg_attachment		 		= Mage::getStoreConfig('themeoptions_theming/content_theme/container_background_attachment', $storeId);
	$content_container_bg_position			 		= Mage::getStoreConfig('themeoptions_theming/content_theme/container_background_postition', $storeId);
	
	$content_inner_bg_color		 							= Mage::getStoreConfig('themeoptions_theming/content_theme/inner_background_color', $storeId);
	$content_inner_bg_image		 							= Mage::getStoreConfig('themeoptions_theming/content_theme/inner_background_image', $storeId);
	$content_inner_bg_repeat		 						= Mage::getStoreConfig('themeoptions_theming/content_theme/inner_background_repeat', $storeId);
	$content_inner_bg_attachment		 				= Mage::getStoreConfig('themeoptions_theming/content_theme/inner_background_attachment', $storeId);
	$content_inner_bg_position			 				= Mage::getStoreConfig('themeoptions_theming/content_theme/inner_background_postition', $storeId);	
	
	$content_heading_color			 						= Mage::getStoreConfig('themeoptions_theming/content_theme/heading_color', $storeId);	
	$content_text_color			 								= Mage::getStoreConfig('themeoptions_theming/content_theme/text_color', $storeId);
	$content_link_color			 								= Mage::getStoreConfig('themeoptions_theming/content_theme/link_color', $storeId);				
	$content_hover_color			 							= Mage::getStoreConfig('themeoptions_theming/content_theme/hover_color', $storeId);		
	$content_active_color			 							= Mage::getStoreConfig('themeoptions_theming/content_theme/active_color', $storeId);	
?>

.main-container,.home-gallery-container,.home-content-container,.footer-info-top,.footer-info-container {
		background:<?php echo $content_container_bg_color?><?php if($content_container_bg_image):?> url(<?php echo $media_path.$content_container_bg_image?>) <?php echo $content_container_bg_repeat?>  <?php echo $content_container_bg_attachment?> <?php echo $content_container_bg_position?><?php endif;?>;
}
.main {
		background:<?php echo $content_inner_bg_color?><?php if($content_inner_bg_image):?> url(<?php echo $media_path.$content_inner_bg_image?>) <?php echo $content_inner_bg_repeat?>  <?php echo $content_inner_bg_attachment?> <?php echo $content_inner_bg_position?><?php endif;?>;
		color:<?php echo $content_text_color?>;
}
.home-content,.home-gallery {
	background:<?php echo $content_inner_bg_color?><?php if($content_inner_bg_image):?> url(<?php echo $media_path.$content_inner_bg_image?>) <?php echo $content_inner_bg_repeat?>  <?php echo $content_inner_bg_attachment?> <?php echo $content_inner_bg_position?><?php endif;?>;
}
.main a {
	color:<?php echo $content_link_color?>
}
.main a:hover {
	color:<?php echo $content_hover_color?>
}
h1,h2,h3,h4,h5,h6,.block-title strong,.block .block-subtitle,.product-view .product-shop .product-name h1,.product-view .box-up-sell h2,.page-title h1, .page-title h2,.product-view .box-tags .form-add label,.home-content h3.title {
	color:<?php echo $content_heading_color?>
}
a:hover,.block-tags .actions a:hover,.price-box .price,.regular-price,.regular-price .price,.minimal-price-link .price,.product-name a:hover,.block-viewed .regular-price .price,.block-account .block-content li.current,.block-account .block-content li a:hover  {
	color:<?php echo $content_active_color?>;
}
.button,button,
.pager .pages a.next,
.pager .pages a.previous,
.block-tags .block-content a:hover,
.products-grid .btn-cart,
.products-list .btn-cart,
.product-tabs a:hover,
.product-tabs li.active a,
.product-tabs li.active a:hover,
.home-gallery a.next:hover,
.home-gallery a.prev:hover {
 	background-color:<?php echo $content_active_color?>;
 }

 .pager .pages a:hover, .pager .pages .current {
 	border-color:<?php echo $content_active_color?>;
}

#empty_cart_button,.btn-update,.btn-continue {
	color:<?php echo $content_text_color?>;
}
#empty_cart_button:hover,.btn-update:hover,.btn-continue:hover {
	color:<?php echo $content_active_color?>;
}
#cboxClose:hover,#cboxNext:hover,#cboxPrevious:hover{background-color:<?php echo $content_active_color?>;}
<?php endif;?>
<?php
$footer_status		 												= intval(Mage::getStoreConfig('themeoptions_theming/footer_theme/status', $storeId));
if($footer_status):
	$footer_container_bg_color		 					= Mage::getStoreConfig('themeoptions_theming/footer_theme/container_background_color', $storeId);
	$footer_container_bg_image		 					= Mage::getStoreConfig('themeoptions_theming/footer_theme/container_background_image', $storeId);
	$footer_container_bg_repeat		 					= Mage::getStoreConfig('themeoptions_theming/footer_theme/container_background_repeat', $storeId);
	$footer_container_bg_attachment		 			= Mage::getStoreConfig('themeoptions_theming/footer_theme/container_background_attachment', $storeId);
	$footer_container_bg_position			 			= Mage::getStoreConfig('themeoptions_theming/footer_theme/container_background_postition', $storeId);
	
	$footer_inner_bg_color		 							= Mage::getStoreConfig('themeoptions_theming/footer_theme/inner_background_color', $storeId);
	$footer_inner_bg_image		 							= Mage::getStoreConfig('themeoptions_theming/footer_theme/inner_background_image', $storeId);
	$footer_inner_bg_repeat		 							= Mage::getStoreConfig('themeoptions_theming/footer_theme/inner_background_repeat', $storeId);
	$footer_inner_bg_attachment		 					= Mage::getStoreConfig('themeoptions_theming/footer_theme/inner_background_attachment', $storeId);
	$footer_inner_bg_position			 					= Mage::getStoreConfig('themeoptions_theming/footer_theme/inner_background_postition', $storeId);	
	
	$footer_border_top_size 								= Mage::getStoreConfig('themeoptions_theming/footer_theme/top_border_size', $storeId);
	$footer_border_top_color 								= Mage::getStoreConfig('themeoptions_theming/footer_theme/top_border_color', $storeId);
	
	$footer_heading_color			 							= Mage::getStoreConfig('themeoptions_theming/footer_theme/heading_color', $storeId);	
	$footer_text_color			 								= Mage::getStoreConfig('themeoptions_theming/footer_theme/text_color', $storeId);
	$footer_link_color			 								= Mage::getStoreConfig('themeoptions_theming/footer_theme/link_color', $storeId);				
	$footer_hover_color			 								= Mage::getStoreConfig('themeoptions_theming/footer_theme/hover_color', $storeId);		
?>
.footer-container {
		background:<?php echo $footer_container_bg_color?><?php if($footer_container_bg_image):?> url(<?php echo $media_path.$footer_container_bg_image?>) <?php echo $footer_container_bg_repeat?>  <?php echo $footer_container_bg_attachment?> <?php echo $footer_container_bg_position?><?php endif;?>;
}
.footer-aditional,.social-links h4,.payment-methods h4 {
		background:<?php echo $footer_inner_bg_color?><?php if($footer_inner_bg_image):?> url(<?php echo $media_path.$footer_inner_bg_image?>) <?php echo $footer_inner_bg_repeat?>  <?php echo $footer_inner_bg_attachment?> <?php echo $footer_inner_bg_position?><?php endif;?>;
		color:<?php echo $footer_text_color?>;
}
.footer-container a {
	color:<?php echo $footer_link_color?>
}
.footer-container a:hover {
	color:<?php echo $footer_hover_color?>
}
.bottom-menu-column h4, .footer-container .twitter h4, .footer-container .facebook h4 {
	color:<?php echo $footer_heading_color?>
}
.footer-container .border-top {border-top:<?php echo $footer_border_top_color?> solid <?php echo $footer_border_top_size?>px} 
<?php endif;?>
<?php
$copyright_status		 													= intval(Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/status', $storeId));
if($copyright_status):
	$copyright_container_bg_color		 						= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/container_background_color', $storeId);
	$copyright_container_bg_image		 						= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/container_background_image', $storeId);
	$copyright_container_bg_repeat		 					= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/container_background_repeat', $storeId);
	$copyright_container_bg_attachment		 			= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/container_background_attachment', $storeId);
	$copyright_container_bg_position			 			= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/container_background_postition', $storeId);
	
	$copyright_inner_bg_color		 								= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/inner_background_color', $storeId);
	$copyright_inner_bg_image		 								= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/inner_background_image', $storeId);
	$copyright_inner_bg_repeat		 							= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/inner_background_repeat', $storeId);
	$copyright_inner_bg_attachment		 					= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/inner_background_attachment', $storeId);
	$copyright_inner_bg_position			 					= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/inner_background_postition', $storeId);	
	
	$copyright_text_color			 									= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/text_color', $storeId);
	$copyright_link_color			 									= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/link_color', $storeId);				
	$copyright_hover_color			 								= Mage::getStoreConfig('themeoptions_theming/copyright_box_theme/hover_color', $storeId);		
?>
.copyright-footer {
		background:<?php echo $copyright_container_bg_color?><?php if($copyright_container_bg_image):?> url(<?php echo $media_path.$copyright_container_bg_image?>) <?php echo $copyright_container_bg_repeat?>  <?php echo $copyright_container_bg_attachment?> <?php echo $copyright_container_bg_position?><?php endif;?>;
}
.copyright-footer .footer{
		background:<?php echo $copyright_inner_bg_color?><?php if($copyright_inner_bg_image):?> url(<?php echo $media_path.$copyright_inner_bg_image?>) <?php echo $copyright_inner_bg_repeat?>  <?php echo $copyright_inner_bg_attachment?> <?php echo $copyright_inner_bg_position?><?php endif;?>;
		color:<?php echo $copyright_text_color?>;
}
.copyright-footer .footer a {
	color:<?php echo $copyright_link_color?>
}
.copyright-footer .footer a:hover {
	color:<?php echo $copyright_hover_color?>
}
<?php endif;?>