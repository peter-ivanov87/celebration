<?php
class Pektsekye_OptionConfigurable_Block_Oc_Options extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_options;
    protected $_attributes;    
    protected $_relations;
    protected $_relationData;    
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('optionconfigurable_options');
        $this->setSkipGenerateContent(true);        
        $this->setTemplate('optionconfigurable/oc/options.phtml');
    }


    public function getProduct()
    {
       return Mage::registry('current_product');
    }  
 

    public function getAttributes()
    {
        if (!isset($this->_attributes))
          $this->_attributes = $this->getProduct()->getTypeInstance(true)->getConfigurableAttributesAsArray($this->getProduct());
        return $this->_attributes;
    }    

    
    public function getOptions()
    {
        if (!isset($this->_options)){
          $options = array();
         
          $hasOrder = false;            
          $rData = $this->getRelationData();
          
          if ($this->getProduct()->isConfigurable()){
            $t = 'a';
            $ocAttributes = Mage::getModel('optionconfigurable/attribute')->getAttributes($this->getProduct()->getId());         
            foreach ($this->getAttributes() as $attribute) {
                  
                $id = (int) $attribute['attribute_id'];
                $sortOrder = isset($ocAttributes[$id]['order']) ? (int) $ocAttributes[$id]['order'] : 0;
                $option = array(
                    'type' => $t,
                    'id' => $id,
                    'title' => $this->htmlEscape($attribute['label']),
                    'sort_order' => $sortOrder,
                    'required' => isset($ocAttributes[$id]['required']) ? (int) $ocAttributes[$id]['required'] : 0,
                    'default' =>  isset($ocAttributes[$id]['default']) ? (int) $ocAttributes[$id]['default'] : '',                    
                    'has_parent' => isset($rData['pVIdsByOId'][$t][$id]), 
                    'selection_type' => 'single',
                    'not_selected_value_id' => -1,                         
                    'values' => array()
                );
  
                foreach ($attribute['values'] as $value) {
                 $valueId = (int) $value['value_index']; 
                                 
                  if ($value['label'] == 'not_selected'){
                    $option['not_selected_value_id'] = $valueId;
                    continue;
                  }
                                                                 
                  $option['values'][] = array(
                      'id' => $valueId,
                      'title' => $this->htmlEscape($value['label']),
                      'price' => $this->getPriceValue($value['pricing_value'], $value['is_percent']),
                      'has_parent' => isset($rData['pVIdsByVId'][$t][$valueId]) || isset($rData['pVIdsByOId'][$t][$id]),   
                      'has_children' => isset($rData['cVIdsByVId'][$t][$valueId]) || isset($rData['cOIdsByVId'][$t][$valueId])                                                       
                      );  
                }
              
               $options[] = $option;
               $hasOrder |= $sortOrder > 0;             
            }        
          }

          $t = 'o';
          $ocOptions = Mage::getModel('optionconfigurable/option')->getOptions($this->getProduct()->getId());          
          foreach ($this->getProduct()->getOptions() as $_option) {
            $id = (int) $_option->getOptionId();
            $option = array(
              'type' => $t,            
              'id' => $id,
              'title' => $this->htmlEscape($_option->getTitle()),         
              'sort_order' => $_option->getSortOrder(), 
              'required' => $_option->getIsRequire(),             
              'default' => isset($ocOptions[$id]['default']) ? $ocOptions[$id]['default'] : '', 
              'values' => array()
            );        


            if ($_option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
            
              $option['selection_type'] = $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO ? 'single' : 'multiple';
              
              foreach ($_option->getValues() as $value) {
                $valueId = (int) $value->getOptionTypeId();
                $option['values'][] = array(
                    'id' => $valueId,
                    'title' => $this->htmlEscape($value->getTitle()),
                    'price' => $this->getPriceValue($value->getPrice(), $value->getPriceType()),
                    'has_parent' => isset($rData['pVIdsByVId'][$t][$valueId]) || isset($rData['pVIdsByOId'][$t][$id]),   
                    'has_children' => isset($rData['cVIdsByVId'][$t][$valueId]) || isset($rData['cOIdsByVId'][$t][$valueId])                                                     
                    );
              }
              
             } else {
                $option['has_parent'] = isset($rData['pVIdsByOId'][$t][$id]);                
             }
             
             $options[] = $option;
          }       
         
          if ($hasOrder){   
            usort($options, array(Mage::helper('optionconfigurable'), "sortOptions"));
          }
           
          $this->_options = $options; 
       }
                          
       return $this->_options;                         
    }                            



    public function getRelations()
    {
      if (!isset($this->_relations))
        $this->_relations = Mage::getModel('optionconfigurable/relation')->getRelations($this->getProduct()->getId());
      return $this->_relations;                         
    } 



    public function getRelationData()
    {
      if (!isset($this->_relationData))
        $this->_relationData = Mage::getModel('optionconfigurable/relation')->getRelationData($this->getProduct());
      return $this->_relationData;                         
    }
    
    
    
    public function hasAttributeRelations()
    {
      $relations = $this->getRelations();      
      return count($relations['aoption_to_attribute']) > 0 || count($relations['aoption_to_aoption']) > 0;                         
    }
    
    
    
    public function hasCustomOptions()
    {     
      return count($this->getProduct()->getOptions()) > 0;                         
    } 
    
    
           
    public function isConfigurable()
    {     
      return $this->getProduct()->isConfigurable();                         
    }     
    
    
    
    public function hasAttributes()
    {     
      return count($this->getProduct()->getTypeInstance(true)->getUsedProductIds($this->getProduct())) > 0;                         
    }     
    
    
    
    public function getDataJson()
    {
      $rData = $this->getRelationData();
      $rData['options'] = $this->getOptions();
      
      $rData['hasNotSelected'] = array();      
      $rData['notSelectedValue'] = array();      
      foreach ($this->getOptions() as $option){
        if ($option['type'] == 'a' && $option['not_selected_value_id'] != -1){
          $rData['hasNotSelected'][$option['id']] = 1;         
          $rData['notSelectedValue'][$option['not_selected_value_id']] = 1;
        }  
      }
              
      return Zend_Json::encode($rData);                         
    }



    public function getRelationsJson()
    {
      return Zend_Json::encode($this->getRelations());                         
    }    

	  
	  
    public function getPriceValue($value, $isPercent)
    {
      if ((int)$value == 0)
        return '';        
      return $isPercent ? (float) $value : number_format($value, 2, null, '');
    }	  
	  
	  
	  
	  
	  
    public function getTabUrl()
    {
        return $this->getUrl('adminhtml/oc_options/index', array('_current'=>true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }
        
    public function getTabLabel()
    {
        return $this->__('Option Relations');
    }
        
    public function getTabTitle()
    {
        return $this->__('Option Relations');
    }
        
    public function canShowTab()
    {
        return $this->getProduct()->getId() != null;
    }    
    
    public function isHidden()
    {
        return false;
    }

}
