
var optionConfigurable;
var OptionConfigurable = {};

OptionConfigurable.Images = Class.create({


	v : {a:[],o:[]},	
	o : {a:[],o:[]},	
	templatePattern : /(^|.|\r|\n)({{(\w+)}})/,
	
	previousSwapOptionIds: {a:[],o:[]},	
  previousSwapOptions: [],


	initialize : function(){
	
		this.mainImage =  {src:null}; 	
    if (!this.isEditOrderPage){
      var imgBox = $$('.product-img-box')[0];
      if (imgBox){
        var img = imgBox.down('img');
        if (img)
		      this.mainImage = img;
		  }      
    }
		this.mainImageLinkHref = $(this.mainImageLinkId) ? $(this.mainImageLinkId).href : this.placeholderUrl;    
		this.mainImageSrc = this.mainImage.src;
		this.descriptionTemplate = new Template(this.descriptionToolTip, this.templatePattern);		
		this.imagePopupTemplate = new Template(this.imagePopup, this.templatePattern);
		
		var optionsContainer = $('product-options-wrapper');
		if (optionsContainer && optionsContainer.getWidth() < 590 ){
	    this.layoutGroup = 'narrow'; 	
		} else {
	    this.layoutGroup = 'wide';		    
		}
		
		this.loadImages();
		this.preloadSwapImages();		
	},	


	loadImages : function(){ 
    var t,id,oT,e,dd,dt,pdd,ll,ii,vId,el;

    var l = this.sortedOIds.length;
    for (var i=0;i<l;i++){   
      t = this.sortedOIds[i]['type'];          
      id   = this.sortedOIds[i]['id'];
      fvId = this.sortedOIds[i]['fvId'];
      oT = this.sortedOIds[i]['optionType'];
                 
      e = this.getElementByOptionType(id, oT); 

			this.o[t][id] = {};
			this.isNewOption = true;				
			this.dd = e.up('dd');

      
      this.prepareOption(t, id, e);

      if (oT == 'radio' || oT == 'checkbox') {	
            
        if (!this.isRequired[t][id] && oT == 'radio'){ //add spacer image for None option
          el = $('options_'+id);
          if (el)               
            this.prepareValue(t, id, el, null);        
        }    
            
        ll = this.vIdsByOId[t][id].length;
        for (ii=0;ii<ll;ii++){
          vId = this.vIdsByOId[t][id][ii];

          this.v[t][vId] = {};
          			
          el = this.oldV[t][vId].e;
                 
          this.prepareValue(t, id, el, vId);        
        }      
      
        	
      } else if (oT == 'drop_down' || oT == 'multiple' || oT == 'attribute_select') {	
      
        ll = this.vIdsByOId[t][id].length;
        for (ii=0;ii<ll;ii++){
          vId = this.vIdsByOId[t][id][ii];

          this.v[t][vId] = {};
                           
          this.prepareValue(t, id, e, vId);        
        } 
        
      } 
                      
    }	
  },

	
	prepareOption : function(t, optionId, element){

		switch (this.layout[t][optionId]){
			case 'above' : 
				if (element.type == 'radio' || element.type == 'select-one') {
				  this.dd.className ='optionconfigurable-' + this.layoutGroup + '-above';									
					Element.insert(this.dd, {'top': new Element('div', {'id' : 'optionconfigurable_description_' + t + '_' + optionId, 'style' : 'display:none;'}).addClassName('description')});					
					Element.insert(this.dd, {'top': this.makeImage(t, optionId, null, 'one')});				
					Element.insert(this.dd, {'top': new Element('div').addClassName('spacer').update('&nbsp;')});						
					Element.insert(this.dd.down('.description'), {'after': new Element('div').addClassName('spacer').update('&nbsp;')});	
				} else {
				  this.dd.className ='optionconfigurable-' + this.layoutGroup + '-above-checkbox';					
				}
			break;
			case 'before' : 	
				if (element.type == 'select-one'){					
					this.dd.className ='optionconfigurable-' + this.layoutGroup + '-before-select';	
					Element.insert(element, {'before': this.makeImage(t, optionId, null, null)});			
					Element.insert(element, {'after': this.descriptionTemplate.evaluate({style : 'style="display:none;"', description : '', 't' : t, 'optionId' : optionId})});					
					Element.insert(this.dd, {'bottom': new Element('div').addClassName('spacer').update('&nbsp;')});					
				} else if(element.type == 'radio'){				
					this.dd.className ='optionconfigurable-' + this.layoutGroup + '-before-radio';		
					Element.insert(this.dd, {'top': this.makeImage(t, optionId, null, null)});
					Element.insert(this.dd, {'top': new Element('div').addClassName('spacer').update('&nbsp;')});						
					Element.insert(this.dd, {'bottom': new Element('div').addClassName('spacer').update('&nbsp;')});					
				}	
			break;
			case 'below' :	
				if (element.type == 'radio' || element.type == 'select-one') {	
				  this.dd.className ='optionconfigurable-' + this.layoutGroup + '-below';							
					Element.insert(this.dd, {'bottom':	this.makeImage(t, optionId, null, 'one')});					
					Element.insert(this.dd, {'bottom': new Element('div', {'id' : 'optionconfigurable_description_' + t + '_' + optionId, 'style' : 'display:none;'}).addClassName('description')});					
					Element.insert(this.dd.down('img'), {'before': new Element('div').addClassName('spacer').update('&nbsp;')});						
					Element.insert(this.dd, {'bottom': new Element('div').addClassName('spacer').update('&nbsp;')});	
				} else {
				  this.dd.className ='optionconfigurable-' + this.layoutGroup + '-below-checkbox';				
				}
			break;
			case 'swap' : 		
				if (element.type == 'select-one'){					
					this.dd.className ='optionconfigurable-' + this.layoutGroup + '-swap-select';			
					Element.insert(element, {'after': new Element('div', {'id' : 'optionconfigurable_description_' + t + '_' + optionId, 'style' : 'display:none;'}).addClassName('description')});								
				} else if(element.type == 'radio'){				
					this.dd.className ='optionconfigurable-' + this.layoutGroup + '-swap-radio';		
					Element.insert(this.dd, {'top': new Element('div').addClassName('spacer').update('&nbsp;')});						
					Element.insert(this.dd, {'bottom': new Element('div').addClassName('spacer').update('&nbsp;')});					
				}	
			break;
			case 'pickerswap' : 			
			case 'picker' : 				
				this.dd.className ='optionconfigurable-' + this.layoutGroup + '-picker';			
				Element.insert(element, {'after': new Element('div', {'id' : 'optionconfigurable_description_' + t + '_' + optionId, 'style' : 'display:none;'}).addClassName('description')});								
			break;
			case 'grid' : 
				this.dd.className = 'optionconfigurable-' + this.layoutGroup + '-grid';
				var ul = this.dd.down('ul');
				Element.insert(ul, {'top': new Element('div').addClassName('spacer').update('&nbsp;')});
				Element.insert(ul, {'bottom': new Element('div').addClassName('spacer').update('&nbsp;')});
			break;
			case 'list' : 				
				this.dd.className ='optionconfigurable-' + this.layoutGroup + '-list';				
			break;	
		}	
		
		Element.insert(this.dd, {'bottom': new Element('div').addClassName('optionconfigurable-' + this.layoutGroup + '-note').update(this.note[t][optionId])});
	},
	
	
	
	
	prepareValue : function(t, optionId, element, value){
		
		var valueId = value ? parseInt(value) : null;
		
		if (value)
			var imageUrl = this.thumbnailDirUrl + this.image[t][valueId];
		
		switch (this.layout[t][optionId]){
			
			case 'above' : 
			
				if (value){
					if (this.image[t][valueId]){
						if (element.type == 'radio' || element.type == 'select-one'){			
							this.v[t][valueId].thumbnail = new Image();
							this.v[t][valueId].thumbnail.src = imageUrl;
						} else {
							if (this.isNewOption){
								Element.insert(this.dd, {'top': this.makeImage(t, optionId, valueId, null)});								
								this.isNewOption = false;								
							} else {	
								Element.insert(previousImage, {'after': this.makeImage(t, optionId, valueId, null)});														
							}
							previousImage = $('optionconfigurable_v_image_' + t + '_' + valueId);
							if (element.type == 'select-multiple')
								this.v[t][valueId].selected = false;												
						}
					}
					if (this.description[t][valueId] && element.type == 'checkbox'){
						Element.insert(element.up('li').down('.label'), {'bottom': this.descriptionTemplate.evaluate({style : '', description : this.description[t][valueId], 't' : t, 'optionId' : valueId})});						
					}						
				}	
				
			break;
			case 'before' : 	
			
				if (value){
					if (this.image[t][valueId]){			
						this.v[t][valueId].thumbnail = new Image();
						this.v[t][valueId].thumbnail.src = imageUrl;
					}
					if (this.description[t][valueId] && element.type == 'radio'){
						Element.insert(element.up('li').down('.label'), {'bottom': this.descriptionTemplate.evaluate({style : '', description : this.description[t][valueId], 't' : t, 'optionId' : valueId})});						
					}	
				}	
				
			break;
			case 'below' :	
			
				if (value){
					if (this.image[t][valueId]){
						if (element.type == 'radio' || element.type == 'select-one'){			
							this.v[t][valueId].thumbnail = new Image();
							this.v[t][valueId].thumbnail.src = imageUrl;
						} else {
							if (this.isNewOption){
								Element.insert(this.dd.down('.optionconfigurable-' + this.layoutGroup + '-note'), {'before': this.makeImage(t, optionId, valueId, null)});							
								this.isNewOption = false;								
							} else {	
								Element.insert(previousImage, {'after': this.makeImage(t, optionId, valueId, null)});														
							}
							previousImage = $('optionconfigurable_v_image_' + t + '_' + valueId);
							if (element.type == 'select-multiple')
								this.v[t][valueId].selected = false;											
						}
					}
					if (this.description[t][valueId] && element.type == 'checkbox'){
						Element.insert(element.up('li').down('.label'), {'bottom': this.descriptionTemplate.evaluate({style : '', description : this.description[t][valueId], 't' : t, 'optionId' : valueId})});						
					}					
				}	
				
			break;
			case 'swap' : 
			
				if (value){
					if (this.image[t][valueId]){			
						this.v[t][valueId].thumbnail = new Image();
						this.v[t][valueId].thumbnail.src = imageUrl;
					}
					if (this.description[t][valueId] && element.type == 'radio'){
						Element.insert(element.up('li').down('.label'), {'bottom': this.descriptionTemplate.evaluate({style : '', description : this.description[t][valueId], 't' : t, 'optionId' : valueId})});						
					}	
				}	
				
			break;
			case 'pickerswap' :
			
				if (value && this.image[t][valueId]){	
						this.v[t][valueId].thumbnail = new Image();
						this.v[t][valueId].thumbnail.src = imageUrl;
				}			 		
					
			case 'picker' : 
			
				if (value && this.image[t][valueId]) {
					if (this.isNewOption){
						Element.insert(this.dd, {'top': this.makeImage(t, optionId, valueId, null)});							
						this.isNewOption = false;								
					} else {	
						Element.insert(previousImage, {'after': this.makeImage(t, optionId, valueId, null)});														
					}
					previousImage = $('optionconfigurable_v_image_' + t + '_' + valueId);
				}		
				
			break;
			case 'grid' : 			
			
				Element.insert(element, {'before':  this.makeImage(t, optionId, valueId, null)});
				if (value && this.description[t][valueId])
					Element.insert(element, {'after': this.descriptionTemplate.evaluate({style : '', description : this.description[t][valueId], 't' : t, 'optionId' : valueId})});		
					
			break;
			case 'list' : 	
			
/* 
---------------The folowing javascript code changes html structure from:-------------------
<li>
	<input class="radio  product-custom-option" onclick="opConfig.reloadPrice()" name="options[88]" id="options_88_3" value="192" type="radio">
	<span class="label">
		<label for="options_88_3">
			TITLE HERE ...
			<span class="price-notice">+<span class="price">PRICE HERE ...</span></span>
		</label>
	</span>
</li>
-------------------------to:
<li>
	<label for="options_88_3">
			<img onclick="optionConfigurable.popup(88, 192)" class="optionconfigurable-image" src="http://...jpg">
	</label>
	<span class="content">
		<label for="options_88_3">
			TITLE HERE ...
			<div class="description">DESCRIPTION HERE ...</div>
			<span class="price-notice">+<span class="price">PRICE HERE ...</span></span>
		</label>
		<input class="radio  product-custom-option" onclick="opConfig.reloadPrice()" name="options[88]" id="options_88_3" value="192" type="radio">
	</span>
	<div class="spacer">&nbsp;</div>
</li>
------------------------------
*/			
				var li = element.up('li');

			  Element.insert(li, {'top': this.makeImage(t, optionId, valueId, null)});		
			    
				var content  = li.down('span.label');
				
				content.className = 'content';
				
				Element.insert(content, {'bottom': element});	
				
				if (value){							
					var description = new Element('div').addClassName('description').update(this.description[t][valueId]);
					var price = content.down('span.price-notice');
					if (price)
						Element.insert(price, {'before': description});	
					else
						Element.insert(content.down('label'), {'bottom': description});						
				} else {
					li.addClassName('none');					
				}	
					
				Element.insert(li, {'bottom': new Element('div').addClassName('spacer').update("&nbsp;")});	
			break;	
		}	
	},
	


	observeRadio : function(t, optionId, valueId){
		if (this.layout[t][optionId] == 'above' || this.layout[t][optionId] == 'below'){	
			this.reloadDescription(t, optionId, valueId);
		}	
		this.reloadImage(t, optionId, valueId, 'radio', null);	
		this.o[t][optionId].value = valueId;		
	},
	
	observeCheckbox : function(element, t, optionId, valueId){		
		this.reloadImage(t, optionId, valueId, 'checkbox', element.checked);	
	},
	
	observeSelectOne : function(element, t, optionId){
		var valueId = element.value;
		if (this.layout[t][optionId] == 'pickerswap'){
			this.reloadPickerImage(t, optionId, valueId);		
			this.reloadImage(t, optionId, valueId, 'select-one', null);				 		
		} else if (this.layout[t][optionId] == 'picker'){
			this.reloadPickerImage(t, optionId, valueId);
		} else {
			this.reloadImage(t, optionId, valueId, 'select-one', null);			
	  }
	  
		if (this.layout[t][optionId] == 'before')
			this.reloadDescriptionIcon(t, optionId, valueId);				
		else
			this.reloadDescription(t, optionId, valueId);				
		
		this.o[t][optionId].value = valueId;				
	},
	
	observeSelectMultiple : function(element, t, optionId){
	    var valueId;
			var options = $A(element.options);		
			var l = options.length;
			while (l--){	
			  valueId = options[l].value;
				if (this.image[t][valueId] && this.v[t][valueId].selected !== options[l].selected){									
					this.reloadImage(t, optionId, valueId, 'select-multiple', options[l].selected);						
					this.v[t][valueId].selected = options[l].selected;	
				}	
			}	
	},	
	
	
	
	
	reloadImage : function(t, optionId, valueId, type, checked){
		if (type == 'radio' || type == 'select-one') {
			if (valueId && this.image[t][valueId]){		
				this.showImage(t, optionId, valueId, type);
			} else {
				if (valueId && (this.layout[t][optionId] == 'above' || this.layout[t][optionId] == 'below') && this.description[t][valueId]){
					this.setPlaceholder(t, optionId);
				} else if(valueId && this.layout[t][optionId] == 'before'){
					this.setPlaceholder(t, optionId);				
				} else {
					this.hideImage(t, optionId, valueId, type);
				}
			}
		}	else {
			if (checked && valueId && this.image[t][valueId])		
				this.showImage(t, optionId, valueId, type);
			else
				this.hideImage(t, optionId, valueId, type);			
		}
	},
	
	showImage : function(t, optionId, valueId, type){
		if (this.layout[t][optionId] != 'grid' && this.layout[t][optionId] != 'list'){
			if (type == 'radio' || type == 'select-one') {
				if (this.layout[t][optionId] == 'swap' || this.layout[t][optionId] == 'pickerswap'){
							
					if ($(this.mainImageLinkId)){
            $(this.mainImageLinkId).href = this.imageDirUrl + this.image[t][valueId];				
				  }
				  					
					this.mainImage.src = this.v[t][valueId].image.src;					
					this.resetZoom();
					if (!this.previousSwapOptionIds[t][optionId]){
					  this.previousSwapOptionIds[t][optionId] = 1; 					
            this.previousSwapOptions.push({type:t, id:optionId});
          }          								
				} else {					
					var image = $('optionconfigurable_image_' + t + '_' + optionId);
					if (this.popup[t][optionId] && image.style.cursor != 'pointer'){			
						image.style.cursor = 'pointer';
						image.title = this.imageTitle;                                                                                     
						var popupJs = this.imagePopupTemplate.evaluate({'url' : 'optionConfigurable.imageDirUrl + optionConfigurable.image.'+t+'[optionConfigurable.o.'+t+'[' + optionId + '].value]'});						
						image.onclick = function(){eval(popupJs)};
					}					
					image.src = this.v[t][valueId].thumbnail.src;
					image.show();
				}			
			} else {
				$('optionconfigurable_v_image_' + t + '_' + valueId).show();
			}
		}
	},	
	
	hideImage : function(t, optionId, valueId, type){
		var po,src,linkHref;
			
		if (this.layout[t][optionId] != 'grid' && this.layout[t][optionId] != 'list'){
			if (type == 'radio' || type == 'select-one') {
				if (this.layout[t][optionId] == 'swap' || this.layout[t][optionId] == 'pickerswap'){
				
				  var ar = [];		
					var l = this.previousSwapOptions.length;
					for (var i=0;i<l;i++){
					  po = this.previousSwapOptions[i];
					  if (po.type == t && po.id == optionId){
					    delete this.previousSwapOptionIds[po.type][po.id];
					    continue;
					  }  
					  ar.push(po);   
					}
					this.previousSwapOptions = ar;
					
					var ls = this.previousSwapOptions.last();
					if (ls){
					  var vId = this.o[ls.type][ls.id].value;
					  if (vId){
					    src = this.v[ls.type][vId].image.src; 
					    linkHref = this.imageDirUrl + this.image[t][vId];					    
					  } else {
					    src = this.mainImageSrc;
					    linkHref = this.mainImageLinkHref;						    					  
              this.previousSwapOptionIds = {b:[],o:[]};	
              this.previousSwapOptions = [];					  
					  }  
					} else {
					  src = this.mainImageSrc;	
					  linkHref = this.mainImageLinkHref;						  		
					}
					 	
					if (this.mainImage.src != src){	
					  if ($(this.mainImageLinkId)){
					    $(this.mainImageLinkId).href = linkHref;						  
					  }							
					  this.mainImage.src = src;				
					  this.resetZoom();						
					}									
				} else if (this.layout[t][optionId] == 'before'){
					var image = $('optionconfigurable_image_' + t + '_' + optionId);
					if (image){
			      if (this.popup[t][optionId] && image.style.cursor == 'pointer'){			
				      image.style.cursor = null;
				      image.title = '';
				      image.onclick = null;
			      }					
						image.src = this.spacer;	
					}
				} else {
					var image = $('optionconfigurable_image_' + t + '_' + optionId);
					if (image){
						image.src = this.spacer;
						image.hide();
					}
				}							
			} else if(this.image[t][valueId]){
				$('optionconfigurable_v_image_' + t + '_' + valueId).hide();			
			}
		}
	},
	
	setPlaceholder : function(t, optionId){
			var image = $('optionconfigurable_image_' + t + '_' + optionId);		
			if (this.popup[t][optionId] && image.style.cursor == 'pointer'){			
				image.style.cursor = null;
				image.title = '';
				image.onclick = null;
			}	
			image.src = this.placeholderUrl;		
			image.show();
	},
	
	
	
	
	reloadDescription : function(t, optionId, valueId){	
		if (valueId && this.description[t][valueId])
			this.showDescription(t, optionId, valueId);
		else
			this.hideDescription(t, optionId);
	},
	
	showDescription : function(t, optionId, valueId){	
		var description = $('optionconfigurable_description_' + t + '_' + optionId);
		description.update(this.description[t][valueId]);
		description.show();			
	},
	
	hideDescription : function(t, optionId){	
		var description = $('optionconfigurable_description_' + t + '_' + optionId);
	  if (description)		
		  description.hide();		
	},
	
	
	
	
	reloadDescriptionIcon : function(t, optionId, valueId){	
		if (valueId && this.description[t][valueId])
			this.showDescriptionIcon(t, optionId, valueId);
		else
			this.hideDescriptionIcon(t, optionId);
	},
	
	showDescriptionIcon : function(t, optionId, valueId){	
		$('optionconfigurable_description_dd_' + t + '_' + optionId).update(this.description[t][valueId]);
		$('optionconfigurable_description_' + t + '_' + optionId).show();		
	},
	
	hideDescriptionIcon : function(t, optionId){
	  var container = $('optionconfigurable_description_' + t + '_' + optionId);
	  if (container)
	  	container.hide();		
	},




	reloadPickerImage : function(t, optionId, valueId){
		if (valueId && this.image[t][valueId])
			this.highlightPickerImage(t, valueId);
		if (this.o[t][optionId].value && this.o[t][optionId].value != valueId && this.image[t][this.o[t][optionId].value])
			this.unhighlightPickerImage(t, this.o[t][optionId].value);
	},
	
	highlightPickerImage : function(t, valueId){
		$('optionconfigurable_v_image_' + t + '_' + valueId).className = 'optionconfigurable-picker-selected';
	},
	
	unhighlightPickerImage : function(t, valueId){
		if (this.image[t][valueId])	
		  $('optionconfigurable_v_image_' + t + '_' + valueId).className = 'optionconfigurable-picker-unselected';
	},
	
 	showPickerImage : function(t, optionId, valueId){
		if ((this.layout[t][optionId] == 'picker' || this.layout[t][optionId] == 'pickerswap') && this.image[t][valueId])		
			$('optionconfigurable_v_image_' + t + '_' + valueId).show();
	},	
	
	hidePickerImage : function(t, optionId, valueId){
		if ((this.layout[t][optionId] == 'picker' || this.layout[t][optionId] == 'pickerswap') && this.image[t][valueId]){			
			$('optionconfigurable_v_image_' + t + '_' + valueId).hide();
		}
	},	
	
	
	
	reloadSelect : function(t, optionId, valueId){
		var select = t == 'a' ? $('attribute'+ optionId) : $('select_' + optionId);
		for (var i=0; i < select.options.length; i++) {
			 if (select.options[i].value == valueId) {
				  select.options[i].selected = true;
				  break;
			 }
		}		
		this.observeSelectOne(select, t, optionId, true);
	},
	
	
	preloadSwapImages : function(){
	  var ll,optionId,valueId;	
  	this.toload = 0;	
  	this.loaded = 0;

    var l = this.sortedOIds.length;
    for (var i=0;i<l;i++){   
      t = this.sortedOIds[i]['type'];          
      optionId   = this.sortedOIds[i]['id'];
		  if (this.layout[t][optionId] == 'swap' || this.layout[t][optionId] == 'pickerswap'){
			  ll = this.vIdsByOId[t][optionId].length;
			  while (ll--){
			    valueId = this.vIdsByOId[t][optionId][ll];			  
				  if (this.image[t][valueId]){
					  this.v[t][valueId].image = new Image();
					  this.v[t][valueId].image.src = this.imageDirUrl + this.image[t][valueId];	
					  this.v[t][valueId].image.onload = function(){
					    this.loaded++;
					    if (this.loaded == this.toload)
				  	    this.onDataReady();
					  }.bind(this);
					  this.toload++;
				  }		
			  }	
			}
		}	
		if (this.toload == 0)
	  	this.onDataReady();	
	},

	
	preloadPopupImages : function(){
	  var ll,optionId,valueId;
    var l = this.sortedOIds.length;
    for (var i=0;i<l;i++){   
      t = this.sortedOIds[i]['type'];          
      optionId = this.sortedOIds[i]['id'];
		  if (this.popup[t][optionId]){		
			  ll = this.vIdsByOId[t][optionId].length;
			  while (ll--){
			    valueId = this.vIdsByOId[t][optionId][ll];
				  if (this.image[t][valueId]){
					  this.v[t][valueId].image = new Image();					  
					  this.v[t][valueId].image.src = this.imageDirUrl + this.image[t][valueId];	
				  }		
			  }	
			}
		}		
	},	
	
	resetImage : function(t, optionId, valueId, type){
    if (this.layout[t][optionId] == 'pickerswap'){	
			this.unhighlightPickerImage(t, valueId);  
			this.hideImage(t, optionId, valueId, type);	
			this.hideDescription(t, optionId);
		} else if (this.layout[t][optionId] == 'picker'){
			this.unhighlightPickerImage(t, valueId);
			this.hideDescription(t, optionId);
		}	else {
			this.hideImage(t, optionId, valueId, type);	
			if ((this.layout[t][optionId] == 'above' || this.layout[t][optionId] == 'below') && (type == 'select-one' || type == 'radio')){
				this.hideDescription(t, optionId);	
			} else if (this.layout[t][optionId] == 'before' && type == 'select-one' ){				
				this.hideDescriptionIcon(t, optionId);			
			}
			if (type == 'select-multiple')
		    this.v[t][valueId].selected = false;						
		}	
	},	
	
	resetZoom : function(){
  	if ($('track') && $('handle') && $('zoom_in') && $('zoom_out') && $('track_hint')){
		  Event.stopObserving(this.mainImage,'dblclick');
      Event.stopObserving('zoom_in');
      Event.stopObserving('zoom_out');
      var parent = this.mainImage.parentNode;
      if (!Element.hasClassName(parent,'product-image-zoom')){
          $('track').up().show();
          $('track_hint').show();
          parent.addClassName('product-image-zoom');      
      }
      this.mainImage.style.width = 'auto';
      this.mainImage.style.height = 'auto';    
		  new Product.Zoom(this.mainImage, 'track', 'handle', 'zoom_in', 'zoom_out', 'track_hint');
		} else if ($(this.mainImageLinkId)){
			jQuery('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
		}
  },
	
	
	makeImage : function(t, optionId, valueId, type){
    var element,className,onclick,src,style,title,id;	
		switch (this.layout[t][optionId]){
			case 'above' :
			case 'below' :			
				id = type == 'one' ? 'optionconfigurable_image_' + t + '_' + optionId : 'optionconfigurable_v_image_' + t + '_' + valueId;								
				className  = 'optionconfigurable-image';
				style = 'display:none;';				
				if (valueId && this.image[t][valueId]){
					src  = this.thumbnailDirUrl + this.image[t][valueId];
					if (this.popup[t][optionId]){
						style += 'cursor:pointer;';	
						title = this.imageTitle;
						onclick = this.imagePopupTemplate.evaluate({'url' : '\'' + this.imageDirUrl + this.image[t][valueId] + '\''});					
					}
				} else if (valueId){
					src = this.placeholderUrl;					
				} else {
					src = this.spacer;						
				}					
			break;				
			case 'grid' :
			case 'list' :
				className = 'optionconfigurable-image';
				if (valueId && this.image[t][valueId]){
					src  = this.thumbnailDirUrl + this.image[t][valueId];
					if (this.popup[t][optionId]){
						style = 'cursor:pointer;';	
						title = this.imageTitle;
						onclick = this.imagePopupTemplate.evaluate({'url' : '\'' + this.imageDirUrl + this.image[t][valueId] + '\''});					
					}
				} else if (valueId){
					src = this.placeholderUrl;					
				} else {
					src = this.spacer;						
				}			
				if (!this.popup[t][optionId])
					onclick = 'optionConfigurable.actAsLabel(\''+ t +'\',' + optionId + ', ' + valueId + ')';				
			break;
			case 'before' : 			
				id = 'optionconfigurable_image_' + t + '_' + optionId;
				className  = 'optionconfigurable-image';				
				src  = !valueId ? this.spacer : (!this.image[t][valueId] ? this.placeholderUrl : this.thumbnailDirUrl + this.image[t][valueId]);
			break;
			case 'pickerswap' :								
			case 'picker' : 	
				id = 'optionconfigurable_v_image_' + t + '_' + valueId;
				className = 'optionconfigurable-picker-unselected';
				src = this.pickerImageDirUrl + this.image[t][valueId];				
				onclick = 'optionConfigurable.reloadSelect(\'' + t + '\', ' + optionId + ', ' + valueId + ')';			
			break;
		}			
		
		element = '<img src="'+src+'" class="'+className+'"' + (id ? ' id="'+id+'"' : '') + (style ? ' style="'+style+'"' : '') + (title ? ' title="'+title+'"' : '') + (onclick ? ' onclick="'+onclick+'"' : '') + '/>';							

		return element;
	},
	
	actAsLabel : function(t, id, valueId){
    var element = valueId ? this.oldV[t][valueId].e : (t == 'a' ? $('bundle-option-'+id) : $('options_'+id)); // for None radio option
    if (element.type == 'radio'){
      element.checked |= true;
      this.observeRadio(t, id, element.value, true);
    } else {
      element.checked = !element.checked;    
      this.observeCheckbox(element, t, id, element.value, true); 
    }
	},
	
	onDataReady : function(){
    this.preloadPopupImages();    
	}	

});
