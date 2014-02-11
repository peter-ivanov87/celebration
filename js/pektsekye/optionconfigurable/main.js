

OptionConfigurable.Main = Class.create(OptionConfigurable.Images, {
	
	initialize : function($super){

		Object.extend(this, OptionConfigurable.Data);	
		Object.extend(this, OptionConfigurable.Config);		

    this.oldV = {a:[],o:[]};
    this.oldO = {a:[],o:[]};
    this.indByValue = {a:[],o:[]}; 
    this.dependecyIsSet = false;
		
		if (this.isConfigurable){
      this.spConfig = this.isEditOrderPage ? ProductConfigure.spConfig : spConfig;      
 
      // The next line is to fix IE9 error in file /js/varien/configurable.js function reloadOptionLabels
      // when the not_selected option value is removed and selectedIndex is set to -1 in function configureForValues
      // To reproduce add product to the cart with one attribute not selected. Then click edit on the cart page.
      this.spConfig.values = null;      
    }

    this.reorderElements();
    this.setRequired();
    this.fillAttributeSelects();     
    this.setVariables(); 
    
		$super();    
     		
		this.hideOptions();												
		this.selectDefault();	
		this.observeSubmit();
	},

  
	reorderElements : function(){
    var t,id,oT,e,dd,dt,pdd;
    	
    var l = this.sortedOIds.length;
    for (var i=0;i<l;i++){   
      t = this.sortedOIds[i]['type'];          
      id   = this.sortedOIds[i]['id'];
      oT = this.sortedOIds[i]['optionType'];  

      if (oT == 'attribute_select' && !this.attributesAffected)
         continue;
        
      e = this.getElementByOptionType(id, oT); 
      
      dd = this.getElementDdTag(e);
      dt = this.getElementDtTag(e);      
      
      if (pdd){
        Element.insert(pdd, {'after':dt});
        Element.insert(dt, {'after':dd});
      }
      
      dd.className = i < l - 1 ? '' : 'last';            
      pdd = dd;           
    }	
  },
  

	setRequired : function(){
    var t,id,oT,e,dd,dt;
    	
    var l = this.sortedOIds.length;
    for (var i=0;i<l;i++){   
      t = this.sortedOIds[i]['type'];          
      id   = this.sortedOIds[i]['id'];
      oT = this.sortedOIds[i]['optionType'];
        
      if (oT == 'attribute_select' && !this.attributesAffected)
         continue;
                 
      e = this.getElementByOptionType(id, oT);
      
      dd = this.getElementDdTag(e);
      dt = this.getElementDtTag(e);	

      if (oT == 'attribute_select'){      
        if (this.hasNotSelected[id] && !this.isRequired[t][id])
            this.makeAttributeNotRequired(e, dt);                  
      } else {      
        if (!this.isRequired[t][id] && this.isEditOrderPage && (oT == 'date' || oT ==  'date_time' || oT == 'time')){ 
          this.addDateCompleteValidation(id);      
        }                     
      }
    }

		if (!this.isEditOrderPage && this.hasRequiredOptions)
      Element.insert($('product-options-wrapper'),{'bottom': this.requiredFieldsText});         
  },

  
	makeAttributeNotRequired : function(e, dt){
    e.removeClassName('required-entry');
    label = dt.down('label');
    label.removeClassName('required');         
    label.down('em').hide();
  },
  
  
	makeOptionRequired : function(e, id, dd, dt, oT){	  
	  var t = 'o';	  
    if (oT == 'radio'|| oT == 'checkbox'){         	       
      var l = this.vIdsByOId[t][id].length;
      for (var i=0;i<l;i++)
        this.getRadioInput(id, i).addClassName('validate-one-required-by-name');      
      this.setAdvaiceContainer(id);       
      Element.insert(dd.down('ul'),{'after': new Element('span', {'id' : 'options-'+id+'-container'})});             				
    } else if (oT == 'date' || oT ==  'date_time' || oT == 'time') {        
      this.addDateValidation(id);
    } else if (oT == 'file') {
      if (this.inPreconfigured) {    
        var checkbox = dd.down('input[type="checkbox"]');
        if (checkbox){
          checkbox.hide();
          checkbox.next('.label').hide();
        }
      }
      e.addClassName('required-entry');            
    } else {         
      e.addClassName('required-entry');
    }
    
    var label = dt.down('label');        
    label.addClassName('required');
    Element.insert(label, {'top': new Element('em').update('*')}); 
  },  
    
    
	setAdvaiceContainer : function(id){
	  var input;
	  
	  var t = 'o';
    var l = this.vIdsByOId[t][id].length;
    for (var i=0;i<l;i++){
      input = this.getRadioInput(id, i);
      input.advaiceContainer = 'options-'+id+'-container';
      input.callbackFunction = 'validateOptionsCallback';
    } 	
	},
	
	
	getRadioInput : function(id, i){
    return $('options_'+id+'_'+ (i + 2));	
	},	
	   
	    
	addDateValidation : function(id){
    Validation.add('validate-datetime-'+id, this.requiredOptionText, function(v,e) {
       var optionId = 0;
       e.name.sub(/[0-9]+/, function(match){
          optionId = parseInt(match[0]);
       });
       var cssRule = '.datetime-picker[id^="options_'+optionId+'"]';
       var dateTimeParts = this.isEditOrderPage ? $('product_composite_configure_form_fields').select(cssRule) : $$(cssRule);
       for (var i=0; i < dateTimeParts.length; i++) {
           if (dateTimeParts[i].value == "") return false;
       }
       return true;
    }.bind(this));
  },


	addDateCompleteValidation : function(id){  
    Validation.add('validate-datetime-'+id, this.fieldIsNotCompleteText, function(v,e) {
      var optionId = 0;
      e.name.sub(/[0-9]+/, function(match){
        optionId = parseInt(match[0]);
      });  

      var minuteToZero = false;
      var hasWithValue = false, hasWithNoValue = false;
      var dayId    = 'options_'+optionId+'_day_part';
      var minuteId = 'options_'+optionId+'_minute';
      
      var cssRule = '.datetime-picker[id^="options_'+optionId+'"]';
      var dateTimeParts = $('product_composite_configure_form_fields').select(cssRule);                 
      for (var i=0; i < dateTimeParts.length; i++) {
         if (dateTimeParts[i].id != dayId) {
             if (dateTimeParts[i].value != "") {
             
                if (dateTimeParts[i].id == minuteId && dateTimeParts[i].value == 0)
                  minuteToZero = true;
                  
                hasWithValue = true;
             } else {
                hasWithNoValue = true;
             }
         }
      }
      
      // to fix Magento 1.7.0.0 bug. minutes are set to 00 by default in:
      // magento/app/design/adminhtml/default/default/template/catalog/product/composite/fieldset/options/type/date.phtml
      var complete = hasWithValue ^ hasWithNoValue;      
      if (minuteToZero && !complete)
        complete = true;
        
      return complete;
    });
  },
    
    
	fillAttributeSelects : function(){
    var id,e,ll,ii,options,index;
    
    if (!this.attributesAffected)
      return;
      
    var t  = 'a';
    var oT = 'attribute_select';    
    var l = this.optionIds[t].length;
    for (var i=0;i<l;i++){            
      id  = this.optionIds[t][i];
      e   = this.getElementByOptionType(id, oT);

      this.setDefaultOptionTitle(e);
                     
      options = this.spConfig.getAttributeOptions(id);
      index = 1;          
      ll = options.length;  
      for(ii=0;ii<ll;ii++){                  
        e.options[index] = new Option(this.spConfig.getOptionLabel(options[ii], options[ii].price), options[ii].id);
        e.options[index].config = options[ii];
        index++;
      } 
              
      e.disabled = false;       
      e.nextSetting = null;
      e.childSettings = null;
       
    }   	
  },
  
  
	setDefaultOptionTitle : function(e){
    e.options[0] = new Option(this.defaultOption, '');  
  },  
  
  
	setVariables : function(){
    var type,id,fVId,oT,e;
    	
    var l = this.sortedOIds.length;
    for (i=0;i<l;i++){        
      t = this.sortedOIds[i]['type'];     
      id   = this.sortedOIds[i]['id'];
      oT = this.sortedOIds[i]['optionType'];  

      if (oT == 'attribute_select' && !this.attributesAffected)
         continue;
        
      e = this.getElementByOptionType(id, oT);             
     
      this.setOptionVariables(e, t, id, oT);
      this.setOptionElement(e, t, id, oT);
      this.observeOptionElement(e, t, id, oT);          
    }	
  },	
	
	
	setOptionVariables : function(e, t, id, oT){
            
      this.oldO[t][id] = {};								
      this.oldO[t][id].oT = oT; 
      this.oldO[t][id].visible = true;
                                         
      switch (oT){
        case 'attribute_select' :      
        case 'drop_down' :
        case 'multiple' :                   
        case 'radio' :
        case 'checkbox' :        
        	this.setOptionValueVariables(e, t, id, oT);         	                          
      }
      
      switch (oT){
        case 'attribute_select' :
          this.removeNotSelected(e, t, id);      
        case 'drop_down' :
        case 'multiple' :        
        	this.setIndexByValue(e, t);                        	                          
      }      

  },


	setOptionValueVariables : function(e, t ,id, oT){  
      var vId;	
      			         								 			
      var l = this.vIdsByOId[t][id].length;
      for (var i=0;i<l;i++){
        vId = this.vIdsByOId[t][id][i];
        this.oldV[t][vId] = {};					
        this.oldV[t][vId].visible = true;
        switch (oT){
          case 'attribute_select' :
           this.setValueSpConfig(t, id, vId, i);
           break;
          case 'multiple' :
            this.oldV[t][vId].name = e.options[i].text;
            break;           
          case 'drop_down' :
            this.oldV[t][vId].name = e.options[i+1].text;	          				                                    
        }                          
      }       
  },  


	setValueSpConfig : function(t, id, vId, i){  
      var productId;	
			
		  var spOptions = this.spConfig.getAttributeOptions(id);			
			this.oldV[t][vId].spConfig = spOptions[i];
      this.oldV[t][vId].products = [];			
      var l = spOptions[i].products.length;
      while(l--){
        productId = parseInt(spOptions[i].products[l]);
        this.oldV[t][vId].products.push(productId);
      }      
        
  },
  
  
	removeNotSelected : function(e, t ,id){         								 			
    var l = e.options.length;
    for (var i=1;i<l;i++){
      if (e.options[i].text.startsWith('not_selected')){
        e.options[i] = null;
        this.oldO[t][id].notSelectedInd = i;
        break;            
      }				                                                                     
    }       
  },  
  
   
	setIndexByValue : function(e, t){  
      var vId;	      			         								 			
      var l = e.options.length;
      for (var i=1;i<l;i++){
        vId = parseInt(e.options[i].value);
        this.indByValue[t][vId] = i; 						                                                                     
      }       
  },
  
    
	setOptionElement : function(e, t, id, oT){			 
    var vId,vE;
    
    if (oT == 'radio' || oT == 'checkbox') {	       
      var l = this.vIdsByOId[t][id].length;
      for (var i=0;i<l;i++){
        vId = this.vIdsByOId[t][id][i];
        vE = this.getRadioInput(id, i);
        this.oldV[t][vId].e  = vE;
        this.oldV[t][vId].li = vE.up('li');
      }        				
    }
            
    this.oldO[t][id].e = e;
    this.oldO[t][id].dd = this.getElementDdTag(e);
    this.oldO[t][id].dt = this.getElementDtTag(e);						

	},		


	observeOptionElement : function(e, t, id, oT){
    var vId,el;
    
    if (oT == 'radio' || oT == 'checkbox') {		
      var l = this.vIdsByOId[t][id].length;								
      if (oT == 'radio'){
        for (var i=0;i<l;i++){
          vId = this.vIdsByOId[t][id][i];
          $('options_'+id+'_'+ (i+2)).observe('click', this.observeRadio.bind(this, t, id, vId, true));
        }							
      } else {
        for (var i=0;i<l;i++){
          vId = this.vIdsByOId[t][id][i];         
          el = $('options_'+id+'_'+ (i+2));            
          el.observe('click', this.observeCheckbox.bind(this, el, t, id, vId, true));
        }						
      }						
    } else if (oT == 'drop_down' || oT == 'attribute_select'){
      e.observe('change', this.observeSelectOne.bind(this, e, t, id, true));
    } else if (oT == 'multiple'){
      e.observe('change', this.observeSelectMultiple.bind(this, e, t, id, true));					
    }	
	},
	
	
	reloadElements : function(){	
    var t,id,fVId,oT,e,ll,ii;
    
    var l = this.sortedOIds.length;
    for (var i=0;i<l;i++){ 	       
      t = this.sortedOIds[i]['type'];     
      id   = this.sortedOIds[i]['id'];
      fVId = this.sortedOIds[i]['firstValueId'];
      oT = this.sortedOIds[i]['optionType']; 

      if (oT == 'attribute_select' && !this.attributesAffected)
         continue;
            
      e = this.getElementByOptionType(id, oT);      
               
      if (oT == 'attribute_select'){         
          this.setDefaultOptionTitle(e);           								 			
          this.removeNotSelected(e, t, id);
          e.nextSetting = null;
          e.childSettings = null;  
      } else if (oT == 'radio'|| oT == 'checkbox'){
        if (this.isRequired[t][id])          
          this.setAdvaiceContainer(id);                          
      } else if (oT == 'date' || oT ==  'date_time' || oT == 'time'){
        if (this.isRequired[t][id])
          this.addDateValidation(id);        
        else
          this.addDateCompleteValidation(id);                
      }
                     
      this.setOptionElement(e, t, id, oT);  
      this.observeOptionElement(e, t, id, oT);         
    }			
	},

	
	hideOptions : function(){
		var t,l,ii,id;
		
		var ac = 0;
		var i = 2;
		while(i--){
      t = i== 0 ? 'a' : 'o';
      
      if (t == 'a' && !this.attributesAffected)
         continue;
               
      l = this.optionIds[t].length;			
      for (ii=0;ii<l;ii++){	
        id = this.optionIds[t][ii];
        if (this.cOIdsByOId[t][id]){
          this.reloadOptions(t, id, 'a', [], []);
          this.reloadOptions(t, id, 'o', [], []);          
        } else if (t == 'a' && ac > 0){
          this.reloadValues(t, id, []);	
          this.hideOption(t, id);        
        }
        if (t == 'a')
          ac++;          
      }   	
		}
		this.dependecyIsSet = true;		
	},	


	reloadOptions : function(t, id, tt, optionIds, valueIds){
    var oId,option,vIds,ndVIds;

    var vIdsByO = this.groupByOption(tt, valueIds);

    var c = this.cOIdsByOId[t][id];
    if (c && c[tt]){
      var l = c[tt].length;
      while (l--){
        oId = c[tt][l];
        option = this.oldO[tt][oId];
        vIds = vIdsByO[oId] ? vIdsByO[oId] : [];
        
        ndVIds = this.notDepVIdsByOId[tt][oId];	        
        if (ndVIds)
          vIds = vIds.concat(ndVIds);
                    
        if (optionIds.indexOf(oId) != -1){ // display entire option	

           vIds = this.vIdsByOId[tt][oId];
 
           switch (option.oT){
            case 'attribute_select' :
              if (!this.checkValues(oId, vIds)){// hide attribute option because its values are out of stock         			
                this.reloadValues(tt, oId, []);	
                this.hideOption(tt, oId);
                break;  	
              }
            case 'drop_down' :
            case 'multiple' :
            case 'radio' :
            case 'checkbox' :         
              this.reloadValues(tt, oId, vIds);
              break;      
            default:
              this.showOption(tt, oId);                          
          }
          
        } else if (vIds.length > 0){ // display children values
        
          this.reloadValues(tt, oId, vIds);		                            
                  
        } else { // hide option	
        
           switch (option.oT){
            case 'attribute_select' : 
            case 'drop_down' :
            case 'multiple' :
            case 'radio' :
            case 'checkbox' :        			
              this.reloadValues(tt, oId, []);	                         
          }        
	
          this.hideOption(tt, oId);					
        }	
      }
    }  
      	
	},

	
	showOption : function(t, id){
	
    var option = this.oldO[t][id];
    
		if (!option.visible){		
		
		  option.dd.show();
		  option.dt.show();

      if (this.isEditOrderPage && this.isRequired[t][id]){
        if (option.oT == 'radio'|| option.oT == 'checkbox'){         	       
          var l = this.vIdsByOId[t][id].length;
          for (var i=0;i<l;i++)
            this.getRadioInput(id, i).addClassName('validate-one-required-by-name');            
        } else if (option.oT == 'date' || option.oT ==  'date_time' || option.oT == 'time'){
          var el = option.dd.select('input[name="validate_datetime_'+ id +'"]')[0];
          el.addClassName('validate-datetime-'+ id);              
        } else {	      
          option.e.addClassName('required-entry');
        }
      }
        		  
			if (option.oT == 'file'){
				var disabled = false;
				
				if (this.inPreconfigured){
	        var inputBox = option.e.up('.input-box');
	        if (!inputBox.visible()){
						var inputFileAction = inputBox.select('input[name="options_'+ id +'_file_action"]')[0];
						inputFileAction.value = 'save_old';
						disabled = true;
					}	
				}
						
				option.e.disabled = disabled;				
			}	  
		  
		  option.visible = true;           		  
		} 			 
	},	
	
	
	hideOption : function(t, id){
	
    var option = this.oldO[t][id];
	
		if (option.visible && !(option.oT == 'attribute_select' && option.notSelectedInd == undefined)){

			if (this.dependecyIsSet){
        switch (option.oT){
          case 'date' :
          case 'date_time':        
          case 'time' :
            this.resetDate(id, option.oT);   
            break;
          case 'field' :
          case 'area' :         
            option.e.value = ''; 
            break;
          case 'file' :        
            if (this.inPreconfigured) {
              var inputBox = option.e.up('.input-box');
              if (!inputBox.visible()){
                var inputFileAction = inputBox.select('input[name="options_'+ id +'_file_action"]')[0];
                inputFileAction.value = '';															
              }	                
            }	        
            option.e.disabled = true;               					                        
        }
			}			

      if (this.isEditOrderPage && this.isRequired[t][id]){
        if (option.oT == 'radio'|| option.oT == 'checkbox'){         	       
          var l = this.vIdsByOId[t][id].length;
          for (var i=0;i<l;i++)
            this.getRadioInput(id, i).removeClassName('validate-one-required-by-name');            
        } else if (option.oT == 'date' || option.oT ==  'date_time' || option.oT == 'time'){
          var el = option.dd.select('input[name="validate_datetime_'+ id +'"]')[0];
          el.removeClassName('validate-datetime-'+ id);    
        } else {	      
          option.e.removeClassName('required-entry');
        }      
			}
			
		  option.dd.hide();
		  option.dt.hide();
		  option.visible = false;
		}  	
		
	},


	resetDate : function(id, oT){
    if (oT ==  'date' || oT == 'date_time') {
      if (this.useCalendar){
	      $('options_'+id+'_date').value = '';      
      } else {     	
        $('options_'+id+'_month').selectedIndex = 0;   
        $('options_'+id+'_day').selectedIndex = 0;
        $('options_'+id+'_year').selectedIndex = 0;
      }
    }          
    if (oT ==  'date_time' || oT == 'time') { 
      $('options_'+id+'_hour').selectedIndex = 0;
      $('options_'+id+'_minute').selectedIndex = 0;
      $('options_'+id+'_day_part').selectedIndex = 0;
    }	
	},		
  
  
	reloadValues : function(t, id, ids){
	  var vId,value;
	  
    var l = this.vIdsByOId[t][id].length;   
    if (l == 0)
    	return; 
    	   
	  var option = this.oldO[t][id];
    switch (option.oT){
      case 'attribute_select' :
      case 'drop_down' :
      case 'multiple' :
        this.clearSelect(t, id);        
        for (var i=0;i<l;i++){
          vId = this.vIdsByOId[t][id][i]; 	
          if (ids.indexOf(vId) != -1)			
              this.showValue(t, id, vId);
        }      
        break;
      case 'radio' :
      case 'checkbox' :         
        for (var i=0;i<l;i++){
          vId = this.vIdsByOId[t][id][i];
          value = this.oldV[t][vId];             			
          if (ids.indexOf(vId) != -1){		
            if (!value.visible)
              this.showValue(t, id, vId);
            else 
              this.resetRadioValue(t, id, vId);
          } else if (value.visible){	    
            this.hideRadioValue(t, id, vId);
          }
        }                          
    }

	},
	

	showValue : function(t, id, vId){
	
	  var value = this.oldV[t][vId];
	  var option = this.oldO[t][id];

    switch (option.oT){
      case 'attribute_select' :
      case 'drop_down' :    
      case 'multiple' :      
        if (option.oT == 'attribute_select' && !this.checkProducts(id, vId))
            return;                 
        var ind = option.e.options.length;        
        if (option.oT == 'attribute_select'){                                      
          option.e.options[ind] = new Option(this.spConfig.getOptionLabel(value.spConfig, value.spConfig.price), value.spConfig.id);
          option.e.options[ind].config = value.spConfig;               
        } else {
          option.e.options[ind] = new Option(value.name, vId);			
        }	        
        this.indByValue[t][vId] = ind;
        this.showOption(t, id);        
        if (option.oT != 'multiple')
				  this.showPickerImage(t, id, vId);				  				        
        break;
      case 'radio' :
      case 'checkbox' : 
        if (this.isEditOrderPage && this.isRequired[t][id])       
          value.e.addClassName('validate-one-required-by-name');            
        value.li.show();	
        this.showOption(t, id);	                         
    }
		
		value.visible = true;
	},
	
	
	resetRadioValue : function(t, id, vId){
	
	  var value = this.oldV[t][vId];
	  var option = this.oldO[t][id];

    if (value.e.checked){  
      this.resetImage(t, id, vId, option.e.type);               
      value.e.checked = false;
    }                          
	},
	
	
	hideRadioValue : function(t, id, vId){
		
	  this.resetRadioValue(t, id, vId);
	  
	  var value = this.oldV[t][vId];

    if (this.isEditOrderPage && this.isRequired[t][id])       
      value.e.removeClassName('validate-one-required-by-name');  
                  
    value.li.hide();	                         
		value.visible = false;
	},	
		
	
  clearSelect : function(t, id){
	  var vId,value;
    var option = this.oldO[t][id];
    	    
    var l = this.vIdsByOId[t][id].length;
    while (l--){
      vId = this.vIdsByOId[t][id][l];
      value = this.oldV[t][vId];      
      if (option.e.value)
	      this.resetImage(t, id, vId, option.e.type);
      if (option.e.type == 'select-one') 
        this.hidePickerImage(t, id, vId);		      	                 
      this.indByValue[t][vId] = null;
      value.visible = false;					  
    } 

    var option = this.oldO[t][id];  			
    option.e.options.length = option.e.type == 'select-one' ? 1 : 0;                			        					       	   
  },
  	

	checkValues : function(id, vIds){
    var l = vIds.length;		
    while (l--){		
      if (this.checkProducts(id, vIds[l]))
        return true;
    }       
    return false;   
	},
	
	
	checkProducts : function(id, valueId){	
		var oId,vId,p,l,pt,pid,t = 'a';

		if (this.oldV[t][valueId].spConfig.label == 'not_selected')
		  return false;
		  
    var l = this.sortedOIds.length;
    for (var i=0;i<l;i++){ 	       
      t = this.sortedOIds[i]['type'];     
      oId = this.sortedOIds[i]['id'];

      if (t == 'o')
        continue;

      vId = oId == id ? valueId : this.oldO[t][oId].e.value;

      if (p == undefined){
      
        if (oId == id) return true;        
        if (vId == '') return false;

        p = this.oldV[t][vId].products; 
         
      } else {
  
        if (oId != id && vId == '') return false;

        pt = [];            
        l = this.oldV[t][vId].products.length;
        while(l--){
          pid = this.oldV[t][vId].products[l];
          if (p.indexOf(pid) != -1)
            pt.push(pid);
        }    

        if (pt.length == 0) return false;

        p = pt;                                
      }               
    }      
    return true;
	},	

	
	observeRadio : function($super, t, id, vId, event){
    var tt,oIds,vIds,c;
    	
		if (this.cOIdsByOId[t][id]){
      var i = 2;
      while(i--){
        tt = i== 0 ? 'a' : 'o';		
        c =	this.cOIdsByVId[t][vId];	
        oIds = c && c[tt] ? c[tt] : [];
        
        c =	this.cVIdsByVId[t][vId];        			
        vIds = c && c[tt] ? c[tt] : [];
        	        
        this.reloadOptions(t, id, tt, oIds, vIds);
      }  								
		}
		
    if (event){
      this.checkedIds[t][id] = [];		
      if (vId)		
        this.checkedIds[t][id].push(parseInt(vId));
      this.selectDefault(t, id);            
    } 
    	
		$super(t, id, vId);    
		this.reloadPrice();			
	},
	
	
	observeCheckbox : function($super, e, t, id, valueId, event){
		var tt,ii,vId,ids,vIds,c;
	  var selectedIds = [];	
	  			
    var l = this.vIdsByOId[t][id].length;
    var i = 2;
    while(i--){
      tt = i== 0 ? 'a' : 'o';	
      ids = [];
      vIds  = [];        			
      for(ii = 0;ii<l;ii++){	
        vId = this.vIdsByOId[t][id][ii];	          			  
        if (this.oldV[t][vId].e.checked){
        
          c =	this.cOIdsByVId[t][vId];    
          if (c && c[tt])													
            ids = ids.concat(c[tt]);
            
          c =	this.cVIdsByVId[t][vId];               
          if (c && c[tt])	            	
            vIds  = vIds.concat(c[tt]);
            
          if (selectedIds.indexOf(vId) == -1)
            selectedIds.push(vId);              
        }												
      }
      if (this.cOIdsByOId[t][id])
        this.reloadOptions(t, id, tt, this.uniq(ids), this.uniq(vIds));					
    }				
	
    if (event){
      this.checkedIds[t][id] = selectedIds;		
      this.selectDefault(t, id);         
    }	
    	
		$super(e, t, id, valueId);    		
		this.reloadPrice();			
	},
	
	
	observeSelectOne : function($super, e, t, id, event){
    var tt,oIds,vIds,c;
        
		var vId = e.value;  		
		if (this.cOIdsByOId[t][id]){		
      var i = 2;
      while(i--){
        tt = i== 0 ? 'a' : 'o';
        c =	this.cOIdsByVId[t][vId];	
        oIds = c && c[tt] ? c[tt] : [];
        
        c =	this.cVIdsByVId[t][vId];        			
        vIds = c && c[tt] ? c[tt] : [];	

        this.reloadOptions(t, id, tt, oIds, vIds);
      } 
		}
		
    if (event){
      this.checkedIds[t][id] = [];		
      if (vId)		
        this.checkedIds[t][id].push(parseInt(vId));
      this.selectDefault(t, id);            
    }		
    
		$super(e, t, id);			
		this.reloadPrice();		
	},
	
	
	observeSelectMultiple : function($super, e, t, id, event){
		var tt,ii,vId,ids,vIds,c;	
	  var selectedIds = [];		
	  				
    var options = $A(e.options);		
    var l = options.length;
    var i = 2;
    while(i--){
      tt = i== 0 ? 'a' : 'o';
      ids = [];
      vIds  = [];	        				
      for(ii = 0;ii<l;ii++){	
        vId = options[ii].value;			  
        if (vId && options[ii].selected){
          c = this.cOIdsByVId[t][vId];    
          if (c && c[tt])													
            ids = ids.concat(c[tt]);
            
          c = this.cVIdsByVId[t][vId]  
          if (c && c[tt])	            	
            vIds  = vIds.concat(c[tt]);
            
          vId = parseInt(vId);            
          if (selectedIds.indexOf(vId) == -1)
            selectedIds.push(vId);             
        }												
      }
      if (this.cOIdsByOId[t][id])				
        this.reloadOptions(t, id, tt, this.uniq(ids), this.uniq(vIds));				
    }			
		
    if (event){
      this.checkedIds[t][id] = selectedIds;
      this.selectDefault(t, id);		   
    }
    
		$super(e, t, id);    
		this.reloadPrice();    			
	},

	
	selectDefault : function(t, fromOptionId){
  	var tt,id;
  		
    var l = this.sortedOIds.length;
    for (var i=0;i<l;i++){        
      tt  = this.sortedOIds[i]['type'];
      id = this.sortedOIds[i]['id'];

      if (tt == 'a' && !this.attributesAffected)
       continue; 
     
      if (fromOptionId){
        if (tt == t && id == fromOptionId)
          fromOptionId = null;
        continue;
      }     
             
      this.selectOptionDefault(tt, id);			   
    }
     
  },
     
     
	selectOptionDefault : function(t, id){
  	var vId,group,ind;
  	     
    var checkedIds = this.checkedIds[t][id];
    if (checkedIds == undefined)
      return;
      
    var option = this.oldO[t][id];        
    if (!option.visible)
      return;
      
    var ids = this.vIdsByOId[t][id];
    var ll = ids.length;		
    while (ll--){
      vId = ids[ll];
      value = this.oldV[t][vId];
      if (value.visible && checkedIds.indexOf(vId) != -1){
        if (option.oT == 'drop_down' || option.oT == 'attribute_select' || option.oT == 'multiple'){
          ind = this.indByValue[t][vId];	
          if (option.e.type == 'select-one')
            option.e.selectedIndex = ind;   
          else 
            option.e.options[ind].selected = true;                                   
        } else if (option.oT == 'radio' || option.oT == 'checkbox'){
          value.e.checked = true;
          if (option.oT == 'radio')
           this.observeRadio(t, id, value.e.value);
          else 
           this.observeCheckbox(value.e, t, id, value.e.value);                                
        }
      }		
    }	

    if (option.oT == 'drop_down' || option.oT == 'attribute_select'){
      this.observeSelectOne(option.e, t, id);		
    } else if (option.oT == 'multiple'){  		
      this.observeSelectMultiple(option.e, t, id);        
    }
  
				
	},
	

	observeSubmit : function(){
	
	  if (!this.attributesAffected)
	    return;
	    	
    Element.insert(this.getOptionsWrapperDiv(),{'bottom': new Element('input', {'type' : 'hidden', 'class' : 'optionconfigurable-not-selected'})});	
    Validation.add('optionconfigurable-not-selected', '', this.onSubmit.bind(this));
	},
	
	
	onSubmit : function(){
		var id,option,t = 'a';

    var l = this.optionIds[t].length;	
    for (var i=0;i<l;i++){
      id = this.optionIds[t][i];
      option = this.oldO[t][id]; 
      if (this.hasNotSelected[id] && (option.visible == false || (!this.isRequired[t][id] && option.e.value == ''))){
          this.addNotSelected(t, id);
      }      
    }

	  return true;
	},
	

	addNotSelected : function(t, id){	  
    var ind = this.oldO[t][id].notSelectedInd - 1;	
    var options = this.spConfig.getAttributeOptions(id);
    this.oldO[t][id].e.options[1] = new Option(this.defaultOption, options[ind].id);
    this.oldO[t][id].e.options[1].config = options[ind];      
    this.oldO[t][id].e.options[1].selected = true;
	},
	
	
	groupByOption : function(t, valueIds){
    var oId,vId;
    		
	  var vIdsByO = [];
    if (valueIds){
      var l = valueIds.length;
      while (l--){
        vId = valueIds[l];
        oId = this.oIdByVId[t][vId];
        
        if (t == 'a' && !this.checkProducts(oId, vId))
            continue;
        
        if (vIdsByO[oId] == undefined)
          vIdsByO[oId] = [];
          				
        vIdsByO[oId].push(vId);
      }      
    }
    
    return vIdsByO;
  },
    
    
	uniq : function(a){
		var l=a.length,b=[],c=[];
		while (l--)
			if (c[a[l]] == undefined) b[b.length] = c[a[l]] = a[l];
		return b;
	},


	getElementByOptionType : function(id, oT){
	  var e;

    switch (oT){
      case 'attribute_select' : 
        e = $('attribute'+id);  
        break;
      case 'drop_down' :
      case 'multiple' :
        e = $('select_'+id);            
        break;
      case 'radio' :
      case 'checkbox' :
        e = $('options_'+id+'_2');            
        break;
      case 'field' :
      case 'area' :
        e = $('options_'+id+'_text');            
        break;                                    
      case 'date' :
      case 'date_time' :
        e = this.useCalendar ? $('options_'+id+'_date') : $('options_'+id+'_month');           
        break;
      case 'time' :
        e = $('options_'+id+'_hour');            
        break;
      case 'file' :
        e = this.getOptionsWrapperDiv().select('input[name="options_'+id+'_file"]')[0];                           
    }
      
    return e;  
	},
	
	
	getOptionsWrapperDiv : function(){	  
	  return this.isEditOrderPage ? $('product_composite_configure_form_fields') : $('product-options-wrapper');
	},
	  
	  
	getElementDdTag : function(e){
    return e.up('dd');
  }, 
  
  
	getElementDtTag : function(e){
    return e.up('dd').previous('dt');
  },      
  
  
	reloadPrice : function(){
    if (this.hasCustomOptions && !this.isEditOrderPage)	
	    opConfig.reloadPrice();
	}	
			
});














