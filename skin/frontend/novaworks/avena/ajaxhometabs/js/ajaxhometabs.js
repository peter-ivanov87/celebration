/*
	default load all tab
*/
function loadData(url, def_tab){
	var $j = jQuery.noConflict();
	
	
	$j(document).ready(function(){
	
		var obj  = $j("#tabs ul li");
		
		// if first time
		obj.each(function(i){
				if($(this).id.split("_")[0] == def_tab){
					$j(this).addClass('active');
				}
			});
	
		// if any click, action normal
		obj.click(function(){
			
			// remove status actived of css in all <li>
			obj.each(function(){

				$j(this).removeClass('active');
			});
			
			
			// load data 
			load($j(this), $j(this).attr("id"));

		});
		
		/*
			add css for mouse over & out
		*/
		obj.mouseover(function(){
			$j(this).addClass('over');
			
		})
		.mouseout(function(){
			$j(this).removeClass('over');
		});
		
		
		/*
			Loading image
		*/
		function startLoad(){
			$j("#loading-mask").css("display", "block");
		}
		
		function endLoad(){
			$j("#loading-mask").css("display", "none");
		}
		
		
		// load data
		function load(obj, id){
			startLoad();
			var val = id.split("_");
			// set background color
			obj.addClass('active');
					
			// set link to request		
			var link = url+"?type="+val[0]+"&mode="+val[1];
			
			// load data
			$j("#content").load(link , function(response, status, xhr) {
					if (status == "error") {
						var msg = "Sorry but there was an error: ";
						$j("#error").html(msg + xhr.status + " " + xhr.statusText);
					  }
					  
				endLoad();
			});
		}
	});
}