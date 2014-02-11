jQuery(function() {
	// Top dropdown
	jQuery(".top-dropdown").mouseenter(function() {
			jQuery(this).click();
	});
	jQuery(".top-dropdown").click(function() {
		jQuery(this).addClass('hover');
		jQuery(this).find("ul").stop(true, true).delay(300).fadeIn(300, "easeOutCubic");
	}).mouseleave(function() {
		jQuery(this).find("ul").stop(true, true).delay(300).fadeOut(300, "easeInCubic");
	});
	// Shopping cart dropdown		
	jQuery(".mini-cart").hover(function() {
			jQuery(this).addClass('hover');
			jQuery(".mini-cart .block-content").stop(true, true).delay(300).fadeIn(500, "easeOutCubic");
		}, function() {
			jQuery(".mini-cart .block-content").stop(true, true).delay(300).fadeOut(500, "easeInCubic");
	});
	//Top menu
	if(jQuery('#default-menu #nav').length) {
	  jQuery("#default-menu #nav").superfish({
		  autoArrows: false,
		  dropShadows: false,
		  animation:   {opacity:'show',height:'show'}
	  });
	  jQuery('#superfish-menu ul#nav li li a').prepend('<span class="menu-arr"></span >');
	}	
	if(jQuery('#wide-menu #nav').length) {
	  jQuery ('#wide-menu #nav li.level-top.parent').hover (function(){
		      jQuery(this).addClass("hover").find('ul:first').stop(true, true).delay(300).fadeIn(300, "easeOutCubic");
		  }, function (){
			   jQuery(this).removeClass("hover").find('ul:first').stop(true, true).delay(300).fadeOut(300, "easeInCubic");
			  })
	}	
	// product box hover
	jQuery(".products-grid .item").hover(function() {
			jQuery(".quickview-box", this).transition({opacity:1});
		}, function() {
			jQuery(".quickview-box", this).transition({opacity:0});
	});
	// product box hover
	jQuery(".home-bottom-container .item").hover(function() {
			jQuery(".hover-disconver", this).transition({opacity:1});
		}, function() {
			jQuery(".hover-disconver", this).transition({opacity:0});
	});
	jQuery(".products-list .products-list-inner").hover(function() {
			jQuery(".quickview-box", this).transition({"display":"block"});
		}, function() {
			jQuery(".quickview-box", this).transition({"display":"none"});
	});

	// Sort by dropdown
	jQuery(".sorter .sort-by").mouseenter(function() {
			jQuery(this).click();
	});
	jQuery(".sorter .sort-by").click(function() {
		jQuery(this).addClass('hover');
		jQuery(this).find("ul").stop(true, true).delay(300).fadeIn(500, "easeOutCubic");
	}).mouseleave(function() {
		jQuery(this).find("ul").stop(true, true).delay(300).fadeOut(500, "easeInCubic");
	});	
	// Limiter dropdown
	jQuery(".sorter .limiter").mouseenter(function() {
			jQuery(this).click();
	});
	jQuery(".sorter .limiter").click(function() {
		jQuery(this).addClass('hover');
		jQuery(this).find("ul").stop(true, true).delay(300).fadeIn(500, "easeOutCubic");
	}).mouseleave(function() {
		jQuery(this).find("ul").stop(true, true).delay(300).fadeOut(500, "easeInCubic");
	});
	//Shopping Options Dropdown
	jQuery(".shopping-option-dropdown").click(function() {
		if(jQuery(this).hasClass('open')){
			jQuery(this).find(".sub-header-nav").stop(true, true).fadeOut(0, "easeInCubic");
			jQuery(".shopping-option-dropdown label i.icon-down-dir").removeClass('icon-up-dir');
			jQuery(this).removeClass('open');
		}else{
		jQuery(this).addClass('open');
		jQuery(".shopping-option-dropdown label i.icon-down-dir").addClass('icon-up-dir');
		jQuery(this).find(".sub-header-nav").stop(true, true).fadeIn(0, "easeOutCubic");
		}	
	});	
	//Shopping Compare Dropdown
	jQuery(".compare-dropdown").click(function() {
		if(jQuery(this).hasClass('open')){
			jQuery(this).find(".ajax-compare").stop(true, true).fadeOut(0, "easeInCubic");
			jQuery(".compare-dropdown label i.icon-down-dir").removeClass('icon-up-dir');
			jQuery(this).removeClass('open');
		}else{
		jQuery(this).addClass('open');
		jQuery(".compare-dropdown label i.icon-down-dir").addClass('icon-up-dir');
		jQuery(this).find(".ajax-compare").stop(true, true).fadeIn(0, "easeOutCubic");
		}	
	});			
	jQuery.each(jQuery('#accordion a.accordion-toggle'), function(i, link){
	    
	        jQuery('#collapse' + 1).collapse({
	            toggle : true,
	            parent : '#accordion'
	        });
	jQuery(link).on('click', 
	    function(){
	        jQuery('#collapse' + 1).collapse('toggle');
	    }
	)
	});
	jQuery('#custom-block-1').each(function(){
                                jQuery(this).addClass('active');
                                jQuery(this).toggle(function(){
                                    jQuery(this).removeClass('active').next().slideUp(200);
                                },function(){
                                    jQuery(this).addClass('active').next().slideDown(200);
                                })
                            }); 	
	jQuery('#custom-block-2').each(function(){
                                jQuery(this).addClass('active');
                                jQuery(this).toggle(function(){
                                    jQuery(this).removeClass('active').next().slideUp(200);
                                },function(){
                                    jQuery(this).addClass('active').next().slideDown(200);
                                })
                            }); 
	jQuery('#custom-block-3').each(function(){
                                jQuery(this).addClass('active');
                                jQuery(this).toggle(function(){
                                    jQuery(this).removeClass('active').next().slideUp(200);
                                },function(){
                                    jQuery(this).addClass('active').next().slideDown(200);
                                })
                            }); 	
	jQuery('.ajaxcart_colorbox').live('mouseenter', function(){
		jQuery(this).colorbox({
			iframe:true,
			opacity:	0.8,
			width:"345",
			close:"<i class=\"icon-cancel-1\"></i>",
			height:"480"
		});
	});	
   jQuery('.quickview_small').live('mouseenter', function(){
   	jQuery(this).colorbox({iframe:true, width:"780px", height:"480px", opacity:0.8, close:"<i class=\"icon-cancel-1\"></i>"});
   	});	
    /* Fixed menu */   
    jQuery(window).scroll(function(){
    	var fixedHeader = jQuery('.header-fixed-container');
    	var scrollTop = jQuery(this).scrollTop();
    	var headerHeight = jQuery('.top-header-container').height() + jQuery('.header-container').height();
    	
    	if(scrollTop > headerHeight){
    		if(!fixedHeader.hasClass('fixed-active')) {
		    	fixedHeader.stop().addClass('fixed-active');
    		}
    	}else{
    		if(fixedHeader.hasClass('fixed-active')) {
		    	fixedHeader.stop().removeClass('fixed-active');
    		}
    	}
    });
});
function setAjaxData(data,iframe){
		if(data.status == 'ERROR'){
			alert(data.message);
		}else{
			if(jQuery('.mini-cart')){
	            jQuery('.mini-cart').replaceWith(data.sidebar);
	        }
					// Shopping cart dropdown		
					jQuery(".mini-cart").hover(function() {
							jQuery(this).addClass('hover');
							jQuery(".mini-cart .block-content").stop(true, true).delay(300).fadeIn(500, "easeOutCubic");
						}, function() {
							jQuery(".mini-cart .block-content").stop(true, true).delay(300).fadeOut(500, "easeInCubic");
					});
			jQuery.colorbox.close();			 					      	        
		}
}
function ajaxcart(url,id) {
    
 url += 'isAjax/1';
 url = url.replace("checkout/cart","ajax/index");
 jQuery("#quick-act-"+id+" .add_to_cart_small").html("<i class=\"icon-spin2 animate-spin\"></i>");
 try {
                jQuery.ajax( {
                    url : url,
                    dataType : 'json',
                    success : function(data) {
                        if(data.status == 'SUCCESS'){    
                            jQuery("#quick-act-"+id+" .add_to_cart_small").html("<i class=\"icon-basket-1\"></i>");        
                        }else{
                            bootbox.alert('<p class="error-msg">' + data.message + '</p>');
                        }   
                        setAjaxData(data,false);
                            
                    }
                });
            } catch (e) {
 }
}
function deletecart(url){
		url = url.replace("checkout/cart","ajax/index");
 		try {
                jQuery.ajax( {
                    url : url,
                    dataType : 'json',
                    success : function(data) {  
                        setAjaxData(data,false);   
                    }
                });
            } catch (e) {
 		}
}
function setAjaxCompareData(data,iframe){
		if(data.status == 'ERROR'){
			alert(data.message);
		}else{
			if(jQuery('.block-compare')){
	            jQuery('.block-compare').replaceWith(data.sidebar);
	        }
			if(jQuery('.ajax-compare')){
	            jQuery('.ajax-compare').replaceWith(data.dropdown);
	        }
						 					      	        
		}
}
function ajaxcompare(url,id) {
 url = url.replace("catalog/product_compare/add","ajax/index/compare");
 jQuery("#quick-act-"+id+" .add_to_compare_small").html("<i class=\"icon-spin2 animate-spin\"></i>");
 try {
                jQuery.ajax( {
                    url : url,
                    dataType : 'json',
                    success : function(data) {
                        if(data.status == 'SUCCESS'){    
                        }else{
                            bootbox.alert('<p class="error-msg">' + data.message + '</p>');
                        }   
                        setAjaxCompareData(data,false);
                        jQuery("#quick-act-"+id+" .add_to_compare_small").html("<i class=\"icon-chart-bar-1\"></i>"); 
                            
                    }
                });
            } catch (e) {
 }
}
function removeCompare(url){
	url = url.replace("catalog/product_compare/remove","ajax/index/removecompare"); 
 		try {
                jQuery.ajax( {
                    url : url,
                    dataType : 'json',
                    success : function(data) {  
                        setAjaxCompareData(data,false);   
                    }
                });
            } catch (e) {
 		}
}
function clearCompare(url){
	url = url.replace("catalog/product_compare/clear","ajax/index/clearcompare"); 
 		try {
                jQuery.ajax( {
                    url : url,
                    dataType : 'json',
                    success : function(data) {  
                        setAjaxCompareData(data,false); 
                    }
                });
            } catch (e) {
 		}
}
function ajaxwishlist(url,id) {
 url = url.replace("wishlist/index/add","ajax/index/addwishlist");
 jQuery("#quick-act-"+id+" .add_to_wishlist_small").html("<i class=\"icon-spin2 animate-spin\"></i>");
 try {
                jQuery.ajax( {
                    url : url,
                    dataType : 'json',
                    success : function(data) {
                        if(data.status == 'SUCCESS'){  
							if(jQuery('.count-wishlist-box')){
	            				jQuery('#count-wishlist-'+id).replaceWith(data.sidebar);
	            				jQuery('.wishlist-link').replaceWith(data.wishlist_header);
	       					 }                        	  
                        }else{
                            bootbox.alert('<p class="error-msg">' + data.message + '</p>');
                        }   
                        jQuery("#quick-act-"+id+" .add_to_wishlist_small").html("<i class=\"icon-wishlist\"></i>");
                            
                    }
                });
            } catch (e) {
 }
}