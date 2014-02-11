
var OptionConfigurableImage = {};
var optionConfigurableImage = {};
OptionConfigurableImage.Main = Class.create({

	uploaders : [],         	  		
	
	initialize : function(){
		Object.extend(this, OptionConfigurableImage.Config);
	},
    
  
  loadImage : function(selectId, src){ 
    var img = $('optionconfigurable_link_'+selectId+'_file_img'); 
    img.src = src;
    img.show(); 
    if (this.uploaders[selectId] != undefined)
      $('optionconfigurable_link_'+selectId+'_file-flash').hide();
    $('optionconfigurable_link_'+selectId+'_file_delete').show();		    	
  },


  loadUploader : function(selectId){ 
    var value = $('optionconfigurable_link_'+selectId+'_file_save').value; 
    if (value.isJSON()) 
      this.loadImage(selectId, value.evalJSON().url);
    else 
      this.addUploader(selectId);
    $(selectId+'_uploader_place-holder').hide();
    $(selectId+'_uploader_row').show();
  },


  addUploader : function(selectId){

     uploaderOITemplate = new
     Template(OptionConfigurableImage.Config.uploaderTemplate,
     /(^|.|\r|\n)(\[\[(\w+)\]\])/);

     Element.insert('optionconfigurable_image_cell_'+selectId, {'top' :uploaderOITemplate.evaluate({'idName' :'optionconfigurable_link_'+selectId+'_file'})});


     var uploader = new Flex.Uploader('optionconfigurable_link_'+selectId+'_file',OptionConfigurableImage.Config.uploaderUrl, this.uploaderConfig);
     uploader.selectId = selectId;

      uploader.handleSelect = function(event) { 
        this.files = event.getData().files;
        this.checkFileSize(); 
        this.updateFiles();
        this.upload();
      };


      uploader.onFilesComplete = function (files) { 
        var item = files[0];
        if (!item.response.isJSON()) {
          alert(OptionConfigurableImage.Config.expiredMessage); 
          return;
        }

        var response = item.response.evalJSON();
        if (response.error) {
          return;
        }

        this.removeFile(item.id);

        $('optionconfigurable_link_'+this.selectId+'_file_save').value = Object.toJSON(response);
        $('optionconfigurable_link_'+this.selectId+'_file-new').hide();

        optionConfigurableImage.loadImage(this.selectId, response.url);

        $('optionconfigurable_link_'+this.selectId+'_file-old').show();
      };

      this.uploaders[selectId] = 1;

  },


  deleteImage : function(selectId){

    $('optionconfigurable_link_'+selectId+'_file_save').value = '{}';
    $('optionconfigurable_link_'+selectId+'_file_img').hide();
    $('optionconfigurable_link_'+selectId+'_file_delete').hide();
    if (this.uploaders[selectId] != undefined)
      $('optionconfigurable_link_'+selectId+'_file-flash').show(); 
    else
      this.addUploader(selectId); 
  },
  
	changePopup : function(t, optionId){
		var popupCheckbox = $('optionconfigurable_'+t+'_'+optionId+'_popup');
		var layout = $('optionconfigurable_'+t+'_'+optionId+'_layout').value;
		if (layout == 'picker' || layout == 'swap' || layout == 'pickerswap'){
			popupCheckbox.checked = false;
			popupCheckbox.disabled = true;
		} else if(popupCheckbox.disabled){
			popupCheckbox.disabled = false;			
		}	
	},  
  
  setScope : function(element, inputId){
    var input = $(inputId);
    if (element.checked)
      input.disable();
    else
      input.enable();

  //  if (type == 'note' || type == 'description'){
      var clickToEditLink = $(inputId+'_show');    
      if (element.checked)
        clickToEditLink.hide();
      else 
        clickToEditLink.show();        
 //   }
     
  }, 	
  
	showTextArea : function(element){
		var inputId = element.id.sub('_show', '');
		var input = $(inputId);
		var textArea = new Element('textarea', {'id' : inputId, 'name' : input.name}).addClassName('optionconfigurable-textarea');
		textArea.value = input.value;
		element.hide();		
		Element.replace(input, textArea);
		$(inputId + '_hide').show();		
	},
	
	
	hideTextArea : function(element){
		var inputId = element.id.sub('_hide', '');
		var textArea = $(inputId);
		var input = new Element('input', {'id' : inputId, 'name' : textArea.name, 'type' : 'text', 'value' : textArea.value}).addClassName('input-text');
		element.hide();
		Element.replace(textArea, input);
		$(inputId + '_show').show();
	}			
}); 



