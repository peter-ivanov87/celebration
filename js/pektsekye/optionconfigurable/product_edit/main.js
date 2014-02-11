var optionConfigurable = {};
var OptionConfigurable = {};

OptionConfigurable.Main = Class.create({

  childrenSelectChanged : {a:[],o:[]},
	templatePattern : /(^|.|\r|\n)({{(\w+)}})/,

		
	initialize : function(data){

		Object.extend(this, data);
		Object.extend(this, OptionConfigurable.Config);

		this.childrenDetailedSelectTemplate = new Template(this.childrenDetailedSelect, this.templatePattern);
		
		Validation.add('optionconfigurable-relation-field', '', this.saveRelation.bind(this));

	},	
	


	showSelect : function(t, id, vId, sT){
	  var select,o,cOIds,cVIds,sid,s,ll,ii,v;
	  	  		
		if (this.options.length < 2)
		  return;
		  
    select = $('oc_'+t+'_'+vId+'_children_'+sT+'_select');	
        
    if (sT == 'short'){
      select.options.length = 1;
      select.options[0].selected = false;		    		    
    } 
                  
    var n = 1;
    var ind = 1;	      
    var options = '';      		  
    var l = this.options.length;		  
    for (i=0;i<l;i++){
    
      o = this.options[i];

      cOIds = this.childrenOptionIds(t, vId, o.type);
      cVIds = this.childrenValueIds(t, vId, o.type);

      if (this.relationIsPossible(t, id, o.type, o.id)){
                  
        if (sT == 'detailed'){

            if (o.values.length == 0){
            
              sid = o.type == 'a' ? '_a' + o.id : '_' + o.id;
              s = cOIds.indexOf(o.id) != -1 ? 'selected' : '';			      
              options +=	'<option '+s+' value="'+sid+'">'+o.title+'</option>';	
              n++;
              
            } else {	
            
              options +=	'<optgroup label="'+o.title+'">';	
              ll = o.values.length;	
              for (ii=0;ii<ll;ii++){
                              
                v = o.values[ii];
                sid = o.type == 'a' ? 'a' + v.id : v.id;		
                s = cOIds.indexOf(o.id) != -1 || cVIds.indexOf(v.id) != -1 ? 'selected' : '';					
                options +=	'<option '+s+' value="'+sid+'">'+v.title+' '+v.price+'</option>';
                n++;
                
              }
              
              options +=	'</optgroup>';
              n++;
              
            }		
                                                            
        } else {
            
          sid = o.type == 'a' ? 'a' + o.id :  o.id;
          select.options[ind] = new Option(o.title, sid);				
          select.options[ind].selected = cOIds.indexOf(o.id) != -1;						
          
          n++;
          ind++;	 
                                  
        }						  		
      }
    }	
        
    if (n > 1){
    
      if (sT == 'detailed'){		        				
        Element.replace(select, this.childrenDetailedSelectTemplate.evaluate({'option_type':t, 'value_id':vId, 'size':(n < 20 ? n : 20), 'options':options}));
        select = $('oc_'+t+'_'+vId+'_children_detailed_select');	        	
      } else {	
        select.size = n < 20 ? n : 20;		
      }

      select.show();	
      select.focus();                  
      $('oc_'+t+'_'+vId+'_show_link').hide();      
      this.unhighlight();		    
    }		
	},


				
	updateChildren : function(t, vId, sT){
    var i,v,isOptionId,cT,oId,ids,ll,vid,rt; 
		
    var select = $('oc_'+t+'_'+vId+'_children_'+sT+'_select');		
    var l = select.options.length;
    		
    if (this.childrenSelectChanged[t][vId] != undefined){      
 
      if (sT == 'detailed'){
        	
        var cOIds  = undefined;              
        var cgVIds = {a:[],o:[]};	   
        var cgOIds = [];
        
        for (i=0;i<l;i++){
          v = select.options[i].value;
          if (v != ''){
          
            isOptionId = false;
            if (v.startsWith('_')){
              v = v.sub('_', '');
              isOptionId = true;
            }  
                   
            cT = 'o';         
            if (v.startsWith('a')){
              v = v.sub('a', '');
              cT = 'a';
            }
            
            intV = parseInt(v); 

            if (select.options[i].selected){            
              if (isOptionId){
                if (cOIds == undefined)
                  cOIds = {}; 
                if (cOIds[cT] == undefined)
                  cOIds[cT] = [];                                                               
                cOIds[cT].push(intV);                
              } else { 
                oId = this.oIdByVId[cT][intV]; 
                if (cgVIds[cT][oId] == undefined){
                  cgVIds[cT][oId] = [];
                  cgOIds.push({t:cT,id:oId});
                }                                                                  
                cgVIds[cT][oId].push(intV);                          	                                       
              }                      
            }        
          }	
        } 
        
	      this.addChildren(t, vId, cOIds, cgVIds, cgOIds);      		  
                                    
      } else { // select type short
      
        var cOIds = this.cOIdsByVId[t];
        var cVIds = this.cVIdsByVId[t];
        
        cOIds[vId] = undefined;

        for (i=0;i<l;i++){
          v = select.options[i].value;                 
          if (v != ''){
                 
            cT = 'o';         
            if (v.startsWith('a')){
              v = v.sub('a', '');
              cT = 'a';
            }		      
        
            intV = parseInt(v);                       
                 
            if (select.options[i].selected){
                                   
              if (cOIds[vId] == undefined) 
                cOIds[vId] = {};
              if (cOIds[vId][cT] == undefined) 
                cOIds[vId][cT] = [];             
              cOIds[vId][cT].push(intV); 
               
              if (cVIds[vId] != undefined && cVIds[vId][cT] != undefined){
                ids = this.vIdsByOId[cT][intV];
                ll = ids.length;
                while(ll--){
                  vid = ids[ll];
                  if (cVIds[vId][cT].indexOf(vid) != -1)
                    cVIds[vId][cT] = cVIds[vId][cT].without(vid);
                }
                if (cVIds[vId][cT].length == 0){
                  rt = cT == 'a' ? 'o' : 'a';
                  if (cVIds[vId][rt] == undefined)
                   cVIds[vId] = undefined;
                  else
                   cVIds[vId][cT] = undefined; 
                }                     
              }               
            }                           
                                            
          }	
        }             
      }
      
      this.resetParent();	            	
    }
		
		select.hide();      		     					
		$('oc_'+t+'_'+vId+'_show_link').show();
    this.childrenSelectChanged[t][vId] = undefined;			
 },



	addChildren : function(t, vId, cOIds, cgVIds, cgOIds) {
    var cT,oId,vIds,vN;
    
    var cVIds = undefined;   
    var l = cgOIds.length;
    for (var i=0;i<l;i++){ 
      cT   = cgOIds[i].t;    
      oId  = cgOIds[i].id;  
      vIds = cgVIds[cT][oId];      
      vN = this.vIdsByOId[cT][oId].length;
      
      if (cT == 'a' && this.hasNotSelected[oId] != undefined)
        vN--;
      
      if (vIds.length != vN){
        if (cVIds == undefined)
          cVIds = {};        
        if (cVIds[cT] == undefined)
          cVIds[cT] = [];                           
        cVIds[cT] = cVIds[cT].concat(vIds);
      } else {  
        if (cOIds == undefined)
          cOIds = {};                                                   
        if (cOIds[cT] == undefined)
          cOIds[cT] = [];                           
        cOIds[cT].push(oId);  
      }           
    }
    
    this.cOIdsByVId[t][vId] = cOIds;              
    this.cVIdsByVId[t][vId] = cVIds;      
  },
  
  
  
	resetParent : function() {
	  var i,l,ii,v,iii,t,cOIds,cVIds,ll,id,o,oId;
	  
    this.pOIdByOId  = {a:[],o:[]};
    this.pOIdsByOId = {a:[],o:[]};
    this.pVIdsByOId = {a:[],o:[]}; 
    this.pVIdsByVId = {a:[],o:[]};
    
    var ol = this.options.length;    
          
    for (i=0;i<ol;i++){
      o = this.options[i];
      l = o.values.length;	
      for (ii=0;ii<l;ii++){                
         v = o.values[ii];
         for(iii=0;iii<2;iii++){
           t = iii == 0 ? 'a' : 'o';
           cOIds = this.cOIdsByVId[o.type][v.id];
           if (cOIds != undefined && cOIds[t] != undefined){
             ll = cOIds[t].length;
             while (ll--) {
              id = cOIds[t][ll];             
              if (this.pOIdByOId[t][id] == undefined)
                this.pOIdByOId[t][id] = {}; 
              if (this.pVIdsByOId[t][id] == undefined){
                this.pVIdsByOId[t][id] = {};
                this.pVIdsByOId[t][id][o.type] = [];
              }  
              this.pOIdByOId[t][id][o.type] = this.oIdByVId[o.type][v.id];
              this.pVIdsByOId[t][id][o.type].push(v.id);
             }
           }
           cVIds = this.cVIdsByVId[o.type][v.id];
           if (cVIds != undefined && cVIds[t] != undefined){                      
             ll = cVIds[t].length;
             while (ll--){
              id = cVIds[t][ll]; 
              oId = this.oIdByVId[t][id];
              if (this.pOIdByOId[t][oId] == undefined)
                this.pOIdByOId[t][oId] = {}; 
              if (this.pVIdsByVId[t][id] == undefined){
                this.pVIdsByVId[t][id] = {};
                this.pVIdsByVId[t][id][o.type] = [];
              }                   
              this.pOIdByOId[t][oId][o.type] = this.oIdByVId[o.type][v.id];
              this.pVIdsByVId[t][id][o.type].push(v.id);
             } 
           } 
         }                       
      }
      
    }
        
    for (i=0;i<ol;i++){
      o = this.options[i];
      this.pOIdsByOId[o.type][o.id] = this.getParentIds(o.type, o.id, {a:[],o:[]});
    }  	
      
    this.updateHighlightLinks(); 
	},



	getParentIds : function(t, id, ids){
    if (this.pOIdByOId[t][id] == undefined)
      return ids;
    
    var pt = this.pOIdByOId[t][id]['a'] != undefined ? 'a' : 'o';      
    var pid = this.pOIdByOId[t][id][pt];

    if (ids[pt].indexOf(pid) != -1)
      return ids;   
    
    ids[pt].push(pid);
    
    return this.getParentIds(pt, pid, ids);     
	},
	
	
	
	updateHighlightLinks : function(){
	  var i,ll,ii,state,v;

    var l = this.options.length;          
    for (i=0;i<l;i++){
      o = this.options[i];
      ll = o.values.length;
      
      if (ll == 0){
      
         if (this.highlighted && this.highlighted.t == o.type && this.highlighted.id == o.id)
            continue;
                       
         state = this.pVIdsByOId[o.type][o.id] != undefined ? 'visible' : 'hidden';   
         this.highlightLink('option', o.type, o.id, 'parent', state);  
                             
      } else {	
      
        for (ii=0;ii<ll;ii++){                
           v = o.values[ii];
           
           if (this.highlighted && this.highlighted.t == o.type && this.highlighted.vId == v.id)
              continue;
                                  
           state = this.cOIdsByVId[o.type][v.id] != undefined || this.cVIdsByVId[o.type][v.id] != undefined ? 'visible' : 'hidden';
           this.highlightLink('value', o.type, v.id, 'children', state);                     
                    
           state = this.pVIdsByOId[o.type][o.id] != undefined || this.pVIdsByVId[o.type][v.id] != undefined ? 'visible' : 'hidden';
           this.highlightLink('value', o.type, v.id, 'parent', state);                      
        }                       
      }      
    }		
	},
		
		
		
	highlightLink : function(lT, t, lId, d, state){
	  $("oc_"+lT+"_"+t+"_"+lId+"_highlight_"+d+"_link").className = 'optionconfigurable-highlight_link_'+state;
	},		
		
		
		
	highlightAction : function(t, id, vId, dr){
	  var i,tt,l,oId,vIds,ll;
	  
    var highlight = true;
    
    if (this.highlighted != undefined){        
      if (this.highlighted.t == t && this.highlighted.id == id && this.highlighted.vId == vId && this.highlighted.dr == dr){
        highlight = false;      
      } else {
        this.unhighlight();			  			        
      }
    }


	  var ids = {};
	  var oIds = {};
	  

    if (dr == 'parent'){
      if (vId != null && this.pVIdsByVId[t][vId]){
        ids =	this.pVIdsByVId[t][vId]
      } 
      if (this.pVIdsByOId[t][id]){
        for(i=0;i<2;i++){
          tt = i == 0 ? 'a' : 'o';
          if (this.pVIdsByOId[t][id][tt]){
            if (ids[tt])
              ids[tt] = ids[tt].concat(this.pVIdsByOId[t][id][tt]);
            else 
              ids[tt] = this.pVIdsByOId[t][id][tt];       
          }      
        }        
      }
    } else { 
      if (this.cVIdsByVId[t][vId] != undefined)
        ids =	this.cVIdsByVId[t][vId]; 
      
      if (this.cOIdsByVId[t][vId] != undefined)
        oIds = this.cOIdsByVId[t][vId];        
    }
    
	  for(i=0;i<2;i++){
      tt = i == 0 ? 'a' : 'o';
      
      if (ids[tt] != undefined){
        l = ids[tt].length;
        while(l--)
          this.highlightCell(tt, null, ids[tt][l], dr, highlight);
      }
      
      if (oIds[tt] != undefined){
        l = oIds[tt].length;
        while(l--){
          oId = oIds[tt][l];
          vIds = this.vIdsByOId[tt][oId];
          ll = vIds.length;
          if (ll == 0){
            this.highlightCell(tt, oId, null, dr, highlight);
          } else {
            while(ll--)
              this.highlightCell(tt, null, vIds[ll], dr, highlight);         
          }
        }
      }
        
    }
 
    
	  var lT = vId ? 'value' : 'option';    
	  var lId = vId || id;	
    
    if (highlight){
    
      this.highlightLink(lT, t, lId, dr, 'selected');
      
      this.highlighted = {};        
      this.highlighted.t = t;
      this.highlighted.id = id;
      this.highlighted.vId = vId;
      this.highlighted.dr = dr;
                 	    
    } else {    
    
      var state = 'hidden';
      
      if (dr == 'children'){
        if (this.cOIdsByVId[t][vId] != undefined || this.cVIdsByVId[t][vId] != undefined)
          state = 'visible';               
      } else {               
        if (this.pVIdsByOId[t][id] != undefined || this.pVIdsByVId[t][vId] != undefined)  
          state = 'visible';     
      }
      
      this.highlightLink(lT, t, lId, dr, state);      
      
      this.highlighted = undefined;      
	  }	
	  
	},	



	highlightCell : function(t, id, vId, dr, highlight){
	
	  if (t == 'a' && this.notSelectedValue[vId] != undefined)
	    return;
	    
	  var cId = vId || id;
	  var cT = vId || dr == 'parent' ? 'value' : 'option';	  	
    var cell = $("oc_"+cT+"_"+t+"_"+cId+"_cell");
    if (highlight){
      cell.addClassName('optionconfigurable-row-cell-'+dr+'-highlighted');
    } else {
      cell.removeClassName('optionconfigurable-row-cell-'+dr+'-highlighted');
    }    
	},
	
	
	unhighlight : function(){	
    if (this.highlighted != undefined){        
      this.highlightAction(this.highlighted.t, this.highlighted.id, this.highlighted.vId, this.highlighted.dr);			  			        
    }	
  },

	saveRelation : function(){
	  var i,ii,t,l,iii,id,iiii,tt,dd;

    var d = {'aoption_to_attribute':{},'aoption_to_option':{},'value_to_attribute':{},'value_to_option':{},'aoption_to_aoption':{},'aoption_to_value':{},'value_to_aoption':{},'value_to_value':{}};	
    
    var k = [{
      'a' :{
        'a' :'aoption_to_attribute',
        'o' :'aoption_to_option'
      },
      'o' :{
        'a' :'value_to_attribute',
        'o' :'value_to_option'
      }
      },{    
      'a' :{
        'a' :'aoption_to_aoption',
        'o' :'aoption_to_value'
      },
      'o' :{
        'a' :'value_to_aoption',
        'o' :'value_to_value'
      }
    }];
    
    var ids = [this.cOIdsByVId, this.cVIdsByVId];
    
    i = 2;    
    while (i--){    
      ii = 2;
      while (ii--){
       t = ii== 0 ? 'a' : 'o';
       l = this.valueIds[t].length;
       for (iii=0;iii<l;iii++){
        id = this.valueIds[t][iii];
        if (ids[i][t][id] != undefined){
          iiii = 2;
          while (iiii--){
           tt = iiii== 0 ? 'a' : 'o';
           if (ids[i][t][id][tt] != undefined && ids[i][t][id][tt].length > 0)
              d[k[i][t][tt]][id] = d[k[i][t][tt]][id] != undefined ? d[k[i][t][tt]][id].concat(ids[i][t][id][tt]) : ids[i][t][id][tt];                     
          } 		  
        }		 
       }     
      }
    }
	
    $('optionconfigurable_relation_field').value = Object.toJSON(d); 

    return true;	
	},


	onChildrenSelectChange : function(t, vId){
    this.childrenSelectChanged[t][vId] = 1; 	
	},
	
           	
	relationIsPossible : function(t, id, oT, oId){

    var parentOptionIdsOfSelf  = this.parentOptionIdsOfSelf(t, id, oT);		      
    var hasNoParentExceptSelf  = this.hasNoParentExceptSelf(oT, oId, t, id);

		return oId != id && parentOptionIdsOfSelf.indexOf(oId) == -1 && hasNoParentExceptSelf;
	},	
	
	
	childrenOptionIds : function(t, vId, oT){
    return this.cOIdsByVId[t][vId] != undefined && this.cOIdsByVId[t][vId][oT] != undefined ? this.cOIdsByVId[t][vId][oT] : [];
	},
	childrenValueIds  : function(t, vId, oT){
    return this.cVIdsByVId[t][vId] != undefined && this.cVIdsByVId[t][vId][oT] != undefined ? this.cVIdsByVId[t][vId][oT] : [];
	},
	parentOptionIdsOfSelf : function(t, id, oT){
    return this.pOIdsByOId[t][id] != undefined && this.pOIdsByOId[t][id][oT] != undefined ? this.pOIdsByOId[t][id][oT] : [];	
	},
	hasNoParentExceptSelf : function(oT, oId, t, id){
	  var rt = t == 'a' ? 'o' : 'a';
    return this.pOIdByOId[oT][oId] == undefined || (this.pOIdByOId[oT][oId][rt] == undefined && this.pOIdByOId[oT][oId][t] != undefined && this.pOIdByOId[oT][oId][t] == id);	
	},
	checkAll : function(input, t, id){
    var vIds = this.vIdsByOId[t][id];
    var l = vIds.length;
    while(l--)
      $('optionconfigurable_'+t+'_'+vIds[l]+'_sd').checked = input.checked;
	}			
			
}); 



