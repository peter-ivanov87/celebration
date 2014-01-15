(function ($, undefined) {
	
	var wrap = 0, wrapOffsetX = 0, wrapOffsetY = 0, spots = new Array(), globals;
	var layerID = 0;
	var StartTime = 500;
	var targetObj = 0;
	var mx = 0, my = 0, mox = 0, moy = 0, mix = 0, miy = 0, ix = 0, iy = 0, ox = 0, oy = 0, iw = 0, ih = 0;
	var cx = 0, cy = 0, cw = 0, ch = 0; // containerX, containerY, containerWidth, containerHeight
	var mouseDown = false, startedDrawing = false, drawing = false, tooltipVisible = false, startMoving = false, moving = false, startScaling = false, scaling = false;
	var scaleHandle = '', moveHandle = '';
	var tooltip = ''; // element reference
	var selectedLayer = undefined;
	
	function Globals() {
		this.settings = { "show_on" : 'always' };
	}
	Globals.prototype.apply = function() {
		var len = spots.length;
		
		for (var i=0; i<len; i++) {
			spots[i].settings['show_on'] = this.settings['show_on'];
			spots[i].apply_settings();
		}
	}
	Globals.prototype.set = function(setting, value) {
		var len = spots.length;
		
		this.settings[setting] = value;
		
		for (var i=0; i<len; i++) {
			spots[i].settings[setting] = this.settings[setting];
		}
	}
	
	function Layer(x, y) {
		this.id = layerID;
		this.time = StartTime;
		this.type = 'spot';
		this.x = x;
		this.y = y;
		this.content = 'Caption Content';	
		this.layer_speed = 500;
		this.layer_style = 'big_white';	
		this.root = '';		
		this.settings = { "visible" : "visible","layer_style" : "big_white","layer_animation" : "fade","layer_easing" : "easeOutExpo","layer_content_type" : "text","layer_speed":500, "show_on" : globals.settings['show_on'], "popup_position" : "right", "content" : "Caption Content", "tooltip_width" : 200, "tooltip_auto_width" : true };
		StartTime = StartTime+300;
		layerID++;
	}
	Layer.prototype.init = function() {
		wrap.append('<div class="hb-spot hb-spot-object" id="hb-spot-' + this.id + '"><div class="hb-tooltip-wrap"><div class="tp-caption hb-tooltip"></div></div></div>');
		this.root = $('#hb-spot-' + this.id);
		
		this.root.css({ "left" : this.x, "top" : this.y, "margin-left" : -this.width/2, "margin-top" : -this.height/2 });
		
		this.apply_settings();
	}
	Layer.prototype.start_moving = function() {
		ix = this.x;
		iy = this.y;
	}
	Layer.prototype.move = function() {
		this.x = (ix + mox + this.width/2 > cw) ? cw - this.width/2 : (ix + mox - this.width/2 < 0) ? this.width/2 : ix + mox;
		this.y = (iy + moy + this.height/2 > ch) ? ch - this.height/2 : (iy + moy - this.height/2 < 0) ? this.height/2 : iy + moy;
		
		this.root.css({
			"left" : this.x,
			"top" : this.y
		});
	}
	Layer.prototype.select = function() {
		enable_form();
		$('.hb-spot-object.selected').removeClass('selected');
		this.root.addClass('selected');
		
		selectedLayer = this;
		update_settings();
	}
	Layer.prototype.del = function() {
		this.deselect();
		disable_form();
		
		this.root.remove();
		spots[this.id] = null;
	}
	Layer.prototype.deselect = function() {
		this.root.removeClass('selected');
		selectedLayer = undefined;
	}
	Layer.prototype.apply_settings = function() {
		this.root.removeClass('left').removeClass('top').removeClass('bottom').removeClass('right').removeClass('mouseover').removeClass('always').removeClass('click').removeClass('visible').removeClass('invisible');
		this.root.find('.hb-tooltip').removeClass('big_white').removeClass('big_orange').removeClass('big_black').removeClass('medium_grey').removeClass('small_text').removeClass('medium_text').removeClass('large_text').removeClass('very_large_text').removeClass('very_big_white').removeClass('very_big_black').removeClass('boxshadow').removeClass('black').removeClass('noshadow');
		this.root.addClass(this.settings['popup_position']);
		this.root.addClass(this.settings['show_on']);
		this.root.addClass(this.settings['visible']);
		this.root.find('.hb-tooltip').addClass(this.settings['layer_style']);
		$('#time-start').val(this.time);
		$('#layer-speed').val(this.layer_speed);
		this.settings['content'] = this.settings['content'].replace(/&lt;/g, '<');
		this.settings['content'] = this.settings['content'].replace(/&gt;/g, '>');
		this.root.find('.hb-tooltip').html(this.settings['content']);
				
		if (!this.settings['tooltip_auto_width']) {
			this.root.find('.hb-tooltip-wrap').css({ 'width' : this.settings['tooltip_width'] });
		} else {
			this.root.find('.hb-tooltip-wrap').css({ 'width' : 'auto' });
		}
	}
	
	function Rectangle_Layer(x, y) {
		this.id = layerID;
		this.time = StartTime;
		this.type = 'rect';
		this.x = x;
		this.y = y;
		this.content = 'Caption Content';
		this.speed = 500;
		this.layer_style = 'big_white';
		this.popupPosition = 0;
		this.root = '';
		
		this.success = true;
		this.settings = { "visible" : "invisible","layer_style":"big_white","layer_animation" : "fade","layer_easing" : "easeOutExpo","layer_content_type" : "text","layer_speed":500, "show_on" : globals.settings['show_on'], "popup_position" : "right", "content" : "Caption Content", "tooltip_width" : 200, "tooltip_auto_width" : true };
		StartTime = StartTime+300;
		layerID++;
	}
	Rectangle_Layer.prototype.init = function() {
		wrap.append('<div class="hb-rect-spot hb-spot-object" id="hb-spot-' + this.id + '"><div class="hb-tooltip-wrap"><div class="hb-tooltip"></div></div></div>');
		this.root = $('#hb-spot-' + this.id);
		
		this.root.css({ "left" : this.x, "top" : this.y });
		
		this.apply_settings();
	}
	Rectangle_Layer.prototype.draw = function() {
		this.width = (mox < 16) ? 16 : mox;
		this.height = (moy < 16) ? 16 : moy;
		
		
		// Constrain to edges of the container
		this.width = (this.width + this.x > cw) ? cw - this.x : this.width;
		this.height = (this.height + this.y > ch) ? ch - this.y : this.height;
		
		this.root.css({ "width" : 0, "height" : 0 });
	}
	Rectangle_Layer.prototype.end_drawing = function() {
		this.root.append(scaleHandle);
		this.root.append(moveHandle);
		
		if (this.width < 16 && this.height < 16) {
			this.success = false;
		}
	}
	Rectangle_Layer.prototype.release = function() {
		this.root.remove();
		layerID--;
	}
	Rectangle_Layer.prototype.start_moving = function() {
		ix = this.x;
		iy = this.y;
	}
	Rectangle_Layer.prototype.move = function() {
		this.x = (ix + mox + this.width > cw) ? cw - this.width : (ix + mox < 0) ? 0 : ix + mox;
		this.y = (iy + moy + this.height > ch) ? ch - this.height : (iy + moy < 0) ? 0 : iy + moy;

		this.root.css({
			"left" : this.x,
			"top" : this.y
		});
	}
	Rectangle_Layer.prototype.start_scaling = function() {
		iw = this.width;
		ih = this.height;
	}
	Rectangle_Layer.prototype.scale = function() {
		this.width = (iw + mox < 16) ? 16 : iw + mox;
		this.height = (ih + moy < 16) ? 16 : ih + moy;
		
		// Constrain to edges of the container
		this.width = (this.width + this.x > cw) ? cw - this.x : this.width;
		this.height = (this.height + this.y > ch) ? ch - this.y : this.height;

		this.root.css({
			"width" : this.width,
			"height" : this.height
		});
	}
	Rectangle_Layer.prototype.select = function() {
		enable_form();
		$('.hb-spot-object.selected').removeClass('selected');
		this.root.addClass('selected');
		
		selectedLayer = this;
		update_settings();
	}
	Rectangle_Layer.prototype.del = function() {
		this.deselect();
		disable_form();
		
		this.root.remove();
		spots[this.id] = null;
	}
	Rectangle_Layer.prototype.deselect = function() {
		this.root.removeClass('selected');
		selectedLayer = undefined;
	}
	Rectangle_Layer.prototype.apply_settings = function() {
		this.root.removeClass('left').removeClass('top').removeClass('bottom').removeClass('right').removeClass('always').removeClass('mouseover').removeClass('click').removeClass('visible').removeClass('invisible');
		
		this.root.addClass(this.settings['popup_position']);
		this.root.addClass(this.settings['show_on']);
		this.root.addClass(this.settings['visible']);
		this.root.find('.hb-tooltip').addClass(this.settings['layer_style']);
		$('#time-start').val(this.time);
		$('#layer-speed').val(this.settings['layer_speed']);

		this.root.find('.hb-tooltip').html(this.settings['content']);
		if (!this.settings['tooltip_auto_width']) {
			this.root.find('.hb-tooltip-wrap').css({ 'width' : this.settings['tooltip_width'] });
		} else {
			this.root.find('.hb-tooltip-wrap').css({ 'width' : 'auto' });
		}
	}
	
	$(document).ready(function() {
		init();
		init_events();
		form_action();
		disable_form();
		window.update_json = function() {
			var newspots = new Array(), len = spots.length, j = 0;

			for (i=0; i<len; i++) {
				if (spots[i] != undefined) {
					newspots[j] = spots[i];
					newspots[j].root = undefined;
					j++;
				}
			}

			$('#spots-json').html(JSON.stringify(newspots));
		}
				//alert(newspots);
	});
	
	function init() {
		globals = new Globals();
		
		wrap = $('#hb-map-wrap');
		cx = wrap.offset().left;
		cy = wrap.offset().top;
		
		var img = new Image();
		img.src = wrap.find('img').attr('src');
		
		if (!img.complete) {
			img.onload = function() {
				cw = wrap.width();
				ch = wrap.height();
			}
		} else {
			cw = wrap.width();
			ch = wrap.height();
		}
		
		scaleHandle = '<div class="hb-scale-handle"></div>';
		moveHandle = '<div class="hb-move-handle"></div>';
		
		$('body').prepend('<div id="hb-help-tooltip"></div>');
		tooltip = $('#hb-help-tooltip');
		if ($('#spots-json').val()) {
			rebuild_objects();
		}
	}
	function rebuild_objects() {
		var js = $('#spots-json').html(), i = 0, html = '', tmp = 0;
		spots = $.parseJSON(js);
		
		var len = spots.length;
		for (i=0; i<len; i++) {
			if (spots[i].type == 'spot') {
				tmp = new Layer(spots[i].x, spots[i].y);
				tmp.settings = spots[i].settings;
				tmp.init();
				spots[i].settings.content = spots[i].settings.content.replace(/&lt;/g, '<');
				spots[i].settings.content = spots[i].settings.content.replace(/&gt;/g, '>');
				spots[i] = tmp;
			} else {
				tmp = new Rectangle_Layer(spots[i].x, spots[i].y);
				spots[i].settings.content = spots[i].settings.content.replace(/&lt;/g, '<');
				spots[i].settings.content = spots[i].settings.content.replace(/&gt;/g, '>');
				tmp.settings = spots[i].settings;
				tmp.load(spots[i].width, spots[i].height);
				spots[i] = tmp;
			}
		}
		dynamic_events();
	}
	function init_events() {
		$('#result').on('click', result);
		
		wrap.on('mousedown', function(e) {
			if ($(e.target).hasClass('hb-scale-handle')) {
				startScaling = true;
				targetObj = spots[$(e.target).closest('.hb-spot-object').attr('id').replace('hb-spot-', '')];
				return false;
			}
			if ($(e.target).hasClass('hb-move-handle')) {
				startMoving = true;
				targetObj = spots[$(e.target).closest('.hb-spot-object').attr('id').replace('hb-spot-', '')];
				return false;
			}
			if ($(e.target).hasClass('hb-spot')) {
				startMoving = true;
				targetObj = spots[$(e.target).attr('id').replace('hb-spot-', '')];
				return false;
			}
			if ($(e.target).closest('.hb-spot-object').length == 0 && !$(e.target).hasClass('hb-spot-object')) {
				mouseDown = true;
				return false;
			}
		});
		$(document).on('mousemove', function(e) {
			
			mx = e.pageX;
			my = e.pageY;

			mox = mx - mix;
			moy = my - miy;
			
			// ============= TOOLTIP =============
			if (tooltipVisible) {
				update_tooltip();
			}
			
			if (targetObj === undefined) {
				return;
			}
			
			
			// ============= SCALE =============
			if (startScaling) {
				mix = mx;
				miy = my;
				
				startScaling = false;
				scaling = true;
				
				targetObj.start_scaling();
				return;
			}			
			if (scaling) {
				targetObj.scale();
				return;
			}
			
			// ============= MOVE =============
			if (startMoving) {
				mix = mx;
				miy = my;
				
				startMoving = false;
				moving = true;
				
				targetObj.start_moving();
				return;
			}

			if (moving) {
				targetObj.move();
				return;
			}
			
			// ============= DRAW =============
			if (mouseDown && !startedDrawing) {
				mix = mx;
				miy = my;
				
				targetObj = new Rectangle_Layer(mx - cx, my - cy);
				targetObj.init();
				
				startedDrawing = true;
				drawing = true;
				return;
			}
			
			if (drawing) {
				targetObj.draw();
				return;
			}
			
			update_tooltip();
		});
		$(document).on('mouseup', function(e) {
			
			if (moving || scaling || startMoving || startScaling) {
				moving = false;
				scaling = false;
				startMoving = false;
				startScaling = false;
				
				return;
			}
			
			if (startedDrawing) {
				targetObj.end_drawing();
				if (targetObj.success) {
					// Prevents too small rectangles. Pretty much useless, having in mind the "Layer" class.
					spots.push(targetObj);
					dynamic_events();
				} else {
					targetObj.release();
				}
				startedDrawing = false;
				drawing = false;
			} else {
				if (($(e.target).attr('id') == 'hb-map-wrap' || $(e.target).closest('#hb-map-wrap').length != 0) && mouseDown) {
					targetObj = new Layer(mx - cx, my - cy);
					spots[layerID - 1] = targetObj;
					targetObj.init();
					dynamic_events();
				}
			}
			
			mouseDown = false;			
		});
	}
	function dynamic_events() {
		$('.hb-scale-handle, .hb-move-handle, .hb-spot, .hb-spot-object').off('.hb');
		
		$('.hb-scale-handle').on('mouseover.hb', function() {
			show_tooltip('scale');
		});
		$('.hb-scale-handle').on('mouseout.hb', function() {
			hide_tooltip();
		});
		$('.hb-move-handle').on('mouseover.hb', function() {
			show_tooltip('move');
		});
		$('.hb-move-handle').on('mouseout.hb', function() {
			hide_tooltip();
		});
		$('.hb-spot').on('mouseover.hb', function() {
			show_tooltip('move');
		});
		$('.hb-spot').on('mouseout.hb', function() {
			hide_tooltip();
		});
		$('.hb-spot-object').on('mouseup.hb', function() {
			$(this).toggleClass('visible-tooltip');
			spots[$(this).attr('id').replace('hb-spot-', '')].select();
		});
	}
	function show_tooltip(text) {
		tooltip.html(text);
		tooltip.show();
		tooltip.css({ "left" : mx + 15, "top" : my + 15 });
		
		tooltipVisible = true;
	}
	function update_tooltip() {
		tooltip.css({ "left" : mx + 15, "top" : my + 15 });
	}
	function hide_tooltip() {
		tooltip.hide();
		
		tooltipVisible = false;
	}
	function show_form_add_layer(){
		
	}
	function update_settings() {
		$('#visible-select').val(selectedLayer.settings['visible']);
		$('#layer-style').val(selectedLayer.settings['layer_style']);
		$('#layer-animation').val(selectedLayer.settings['layer_animation']);
		$('#layer-easing').val(selectedLayer.settings['layer_easing']);
		$('#layer_content_type').val(selectedLayer.settings['layer_content_type']);
		$('#layer-speed').val(selectedLayer.settings['layer_speed']);
		$('#time-start').val(selectedLayer.time);
		$('#show-select').val(globals.settings['show_on']);
		$('#position-select').val(selectedLayer.settings['popup_position']);
		$('#content').val(selectedLayer.settings['content']);
		if(selectedLayer.settings['layer_content_type']=='video') {
			$('#content_video').show();
		}else {
			$('#content_video').hide();
		}
		if (selectedLayer.settings['tooltip_auto_width']) {
			$('#tooltip-auto-width').attr('checked', 'checked');
			$('#tooltip-width').attr('disabled', 'disabled').val(selectedLayer.settings['tooltip_width']);
		} else {
			$('#tooltip-auto-width').removeAttr('checked');
			$('#tooltip-width').removeAttr('disabled').val(selectedLayer.settings['tooltip_width']);
		}
		if (selectedLayer.settings['auto_video']) {
			$('#layer_video_autoplay').attr('checked', 'checked');
		} else {
			$('#layer_video_autoplay').removeAttr('checked');
		}
	}
	function form_action() {
		$('#layer-style').on('change', function() {
			if (selectedLayer) {
				selectedLayer.settings['layer_style'] = $(this).val();
				selectedLayer.apply_settings();
			}
		});
		$('#layer-animation').on('change', function() {
			if (selectedLayer) {
				selectedLayer.settings['layer_animation'] = $(this).val();
				selectedLayer.apply_settings();
			}
		});
		$('#layer-easing').on('change', function() {
			if (selectedLayer) {
				selectedLayer.settings['layer_easing'] = $(this).val();
				selectedLayer.apply_settings();
			}
		});
		$('#layer_content_type').on('change', function() {
			if (selectedLayer) {
				selectedLayer.settings['layer_content_type'] = $(this).val();
				selectedLayer.apply_settings();
			}
		});
		$('#layer-speed').on('change', function() {
			if (selectedLayer) {
				selectedLayer.settings['layer_speed'] = $(this).val();
				selectedLayer.apply_settings();
			}
		});
		$('#time-start').on('change', function() {
			if (selectedLayer) {
				selectedLayer.time = $(this).val();
				selectedLayer.apply_settings();
			}
		});
		$('#content').on('change', function() {
			if (selectedLayer) {
				selectedLayer.settings['content'] = $(this).val();
				selectedLayer.apply_settings();
			}
		});
		$('#layer_video_autoplay').on('change', function() {
			if ($(this).attr('checked')) {
				selectedLayer.settings['auto_video'] = true;
			}else{
				selectedLayer.settings['auto_video'] = false;
			}
		});
		$('#delete').on('click', function() {
			if (selectedLayer) {
				selectedLayer.del();
			}
		});
		$('#tooltip-auto-width').on('change', function() {
			if ($(this).attr('checked')) {
				$('#tooltip-width').attr('disabled', 'disabled');
				selectedLayer.settings['tooltip_auto_width'] = true;
			} else {
				$('#tooltip-width').removeAttr('disabled');
				selectedLayer.settings['tooltip_auto_width'] = false;
			}
			selectedLayer.settings['tooltip_width'] = parseInt($('#tooltip-width').val().replace('px', ''));
			selectedLayer.apply_settings();
		});
		$('#tooltip-width').on('change', function() {
			selectedLayer.settings['tooltip_width'] = parseInt($('#tooltip-width').val().replace('px', ''));
			selectedLayer.apply_settings();
		});
	}
	function disable_form() {
		$('#hb-settings-wrap').find('input, textarea, select').attr('disabled', 'disabled');
	}
	function enable_form() {
		$('input, textarea, select').not('#tooltip-width').removeAttr('disabled');
		
		if ($('#tooltip-auto-width').attr('checked')) {
			$('#tooltip-width').attr('disabled', 'disabled');
		}
	}
	function update_json() {
		var newspots = new Array(), len = spots.length, j = 0;
		
		for (i=0; i<len; i++) {
			if (spots[i] != undefined) {
				newspots[j] = spots[i];
				newspots[j].root = undefined;
				j++;
			}
		}
		$('#spots-json').html(JSON.stringify(newspots));
	}
	function generate_html(id) {
		var html = '', len = spots.length, i;
		html += '<div id="layerpot-' + id + '" class="hs-wrap hs-loading">\n';
		html += '<img src="' + wrap.find('img').attr('src') + '">\n';
		for (i = 0; i < len; i++) {
			if (spots[i]) {
				html += '<div class="hs-spot-object" data-layer-style="' + spots[i].layer_style + '" data-type="' + spots[i].type + '" data-x="' + spots[i].x + '" data-y="' + spots[i].y + '" data-width="' + spots[i].width + '" data-height="' + spots[i].height + '" data-popup-position="' + spots[i].settings['popup_position'] + '" data-visible="' + spots[i].settings['visible'] + '" data-tooltip-width="' + spots[i].settings['tooltip_width'] + '" data-tooltip-auto-width="' + spots[i].settings['tooltip_auto_width'] + '">\n';
				html += spots[i].settings.content + '\n';
				html += '</div>\n';
			}
		}
		
		html += '</div>\n';
		update_json();
		return html;
	}
	function generate_js(id) {
		var js = '';
		
		js += '$("#layerpot-' + id + '").layerpot({ "show_on" : "' + globals.settings['show_on'] + '" });';
		
		return js;
	}
	function launch_plugin() {
		
	}
	function result() {
		var id = Math.round(Math.random()*100);
		var html = generate_html(id);
	}
}(jQuery));