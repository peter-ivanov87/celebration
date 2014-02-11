<?php
class MW_AjaxHomeTabs_Block_ShowTabs extends Mage_Core_Block_Template{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
	
	
	public function getTabsHtml(){
				$html = "";
		$html .='<ul id="ajaxnav">	';
						 // get step by step tab
			foreach($this->getTabs() as $tab){
		
				if(!empty($tab['name'])){
					$html .='<li class="level0 level-top" id="'.$tab['name'].'_'.$tab['mode'].'">';
			
                                                $html.="<a href='' class='' onclick='return false;'><span>".$tab['label']."</span></a>";    
					$html .= '</li>	';
				}
			}
                        
                        
                                
		$html .= '</ul>';
		return $html;
	}
	
	public function getTabs(){
		$tab_value =array();
					
	// Check tab in setting by administrator
		$tabs_allowed = Mage::getStoreConfig('ajaxhometabs/tabgeneral/allow_tabs');
		
		
		$tabs = explode("," ,$tabs_allowed);
		
					foreach($tabs as $tab){	
						$label = Mage::getStoreConfig('ajaxhometabs/'.$tab.'/custom_label');
							$label = (empty($label)) ? "No Name" : $label;
							
						$mode = Mage::getStoreConfig('ajaxhometabs/'.$tab.'/view_mode');
						$order = Mage::getStoreConfig('ajaxhometabs/'.$tab.'/order_display');
							$order = (empty($order)) ? 0 : $order;
						
						$tab_value[] = array("name"=>$tab,
											"label"=>$label,
											"mode"=>$mode,
											"order"=>$order);
					}		
					
					$tab_value = $this->orderBy($tab_value, 'order'); 
		return $tab_value;
	}
	
	
			
	
	function orderBy($data, $field) { 
		$code = "return strnatcmp(\$a['$field'], \$b['$field']);"; 
		usort($data, create_function('$a,$b', $code)); return $data; 
	}
	
	public function getDefaultContent() {
    return $this->getLayout()->createBlock("ajaxhometabs/list")->setTemplate("ajaxhometabs/list.phtml")->toHtml();
  }
}