<?php
class Pektsekye_OptionConfigurable_Block_Oc_Optionimages extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_options;
    protected $_attributes;     

    
    public function __construct()
    {
      parent::__construct();
      $this->setId('optionconfigurocble_optionimages');
      $this->setSkipGenerateContent(true);        
      $this->setTemplate('optionconfigurable/oc/optionimages.phtml');
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
        
        if ($this->getProduct()->isConfigurable()){
          $t = 'a';
          $ocAttributes = Mage::getModel('optionconfigurable/attribute')->getAttributes($this->getProduct()->getId(), (int)$this->getProduct()->getStoreId());                
          foreach ($this->getAttributes() as $attribute) {
                            
              $id = (int) $attribute['attribute_id'];
              $sortOrder = isset($ocAttributes[$id]['order']) ? (int) $ocAttributes[$id]['order'] : 0;                            
              $option = array(
                  'type' => $t,
                  'id' => $id,
                  'title' => $this->htmlEscape($attribute['label']),
                  'sort_order' => $sortOrder,                 
                  'note' => isset($ocAttributes[$id]['note']) ? $ocAttributes[$id]['note'] : '',  
                  'store_note' => isset($ocAttributes[$id]['store_note']) ? $ocAttributes[$id]['store_note'] : null,                      
                  'layout' => isset($ocAttributes[$id]['layout']) ? $ocAttributes[$id]['layout'] : '',  
                  'popup' => isset($ocAttributes[$id]['popup']) ? $ocAttributes[$id]['popup'] : 0,
                  'option_type' => 'drop_down',
                  'selection_type' => 'single',                                                           
                  'values' => array()                
              );

              $ocValues = Mage::getModel('optionconfigurable/aoption')->getValues($this->getProduct()->getId(), (int)$this->getProduct()->getStoreId());
              foreach ($attribute['values'] as $value) {
               $valueId = (int) $value['value_index']; 
                               
                if ($value['label'] == 'not_selected'){
                  $option['not_selected_value_id'] = $valueId;
                  continue;
                }

                $image = isset($ocValues[$valueId]['image']) ? $ocValues[$valueId]['image'] : '';                                              
                $option['values'][] = array(
                    'id' => $valueId,
                    'title' => $this->htmlEscape($value['label']),
                    'price' => $this->getPriceValue($value['pricing_value'], $value['is_percent']),
                    'image' => $image,
                    'image_json' => $this->getImageJson($image),                        
                    'description' => isset($ocValues[$valueId]['description']) ? $ocValues[$valueId]['description'] : '',
                    'store_description' => isset($ocValues[$valueId]['store_description']) ? $ocValues[$valueId]['store_description'] : null                                                                                       
                    );  
              }
            
             $options[] = $option;
             $hasOrder |= $sortOrder > 0;             
          }        
        }

        $t = 'o';
        $ocOptions = Mage::getModel('optionconfigurable/option')->getOptions($this->getProduct()->getId(), (int)$this->getProduct()->getStoreId());          
        foreach ($this->getProduct()->getOptions() as $_option) {
          $id = (int) $_option->getOptionId();
          $option = array(
            'type' => $t,            
            'id' => $id,
            'title' => $this->htmlEscape($_option->getTitle()),         
            'sort_order' => $_option->getSortOrder(), 
            'required' => $_option->getIsRequire(),             
            'default' => isset($ocOptions[$id]['default']) ? $ocOptions[$id]['default'] : '', 
            'note' => isset($ocOptions[$id]['note']) ? $ocOptions[$id]['note'] : '',
            'store_note' => isset($ocOptions[$id]['store_note']) ? $ocOptions[$id]['store_note'] : null,                  
            'layout' => isset($ocOptions[$id]['layout']) ? $ocOptions[$id]['layout'] : '',  
            'popup' => isset($ocOptions[$id]['popup']) ? $ocOptions[$id]['popup'] : 0,
            'option_type' => $_option->getType(),                
            'values' => array()
          );        


          if ($_option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
          
            $option['selection_type'] = $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO ? 'single' : 'multiple';
            
            $ocValues = Mage::getModel('optionconfigurable/value')->getValues($this->getProduct()->getId(), (int)$this->getProduct()->getStoreId());              
            foreach ($_option->getValues() as $value) {

              $valueId = (int) $value->getOptionTypeId();
              $image = isset($ocValues[$valueId]['image']) ? $ocValues[$valueId]['image'] : '';
              $option['values'][] = array(
                  'id' => $valueId,
                  'title' => $this->htmlEscape($value->getTitle()),
                  'price' => $this->getPriceValue($value->getPrice(), $value->getPriceType() == 'percent'),
                  'image' => $image,
                  'image_json' => $this->getImageJson($image), 
                  'description' => isset($ocValues[$valueId]['description']) ? $ocValues[$valueId]['description'] : '',
                  'store_description' => isset($ocValues[$valueId]['store_description']) ? $ocValues[$valueId]['store_description'] : null                                                                        
                  );
            }
            
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


    
    public function getImageJson($value)
    {
      $js = '';   
      if ($value != '') {
        $image['url'] = $this->helper('catalog/image')->init(Mage::getModel('catalog/product'), 'thumbnail', $value)->keepFrame(true)->resize(100,100)->__toString();
        $js = Zend_Json::encode($image);
      }
      return $js;
    }
    
    
    public function getConfigJson()
    {
      $config = new Varien_Object();
      $config->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/catalog_product_gallery/upload'));		
      $config->setParams(array('form_key' => $this->getFormKey()));

      $config->setFileField('image');
      $config->setFilters(array(
          'images' => array(
              'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
              'files' => array('*.gif','*.jpg','*.jpeg','*.png')
          )
      ));
      $config->setReplaceBrowseWithRemove(true);
      $config->setWidth('32');
      $config->setHideUploadButton(true);
      return Zend_Json::encode($config->getData());
    }



     public function getLayoutSelect($option)
    {
      $options = array();
      
      switch($option['option_type']){
        case 'radio' :
          $options = array(
            array('value' =>'above' , 'label'=>$this->__('Above Option')),        
            array('value' =>'before', 'label'=>$this->__('Before Option')),
            array('value' =>'below' , 'label'=>$this->__('Below Option')),
            array('value' =>'swap'  , 'label'=>$this->__('Main Image')),
            array('value' =>'grid'  , 'label'=>$this->__('Grid')),    
            array('value' =>'list'  , 'label'=>$this->__('List'))                
          );
        break;
        case 'checkbox' :
          $options = array(
            array('value' =>'above', 'label'=>$this->__('Above Option')),         
            array('value' =>'below', 'label'=>$this->__('Below Option')),
            array('value' =>'grid' , 'label'=>$this->__('Grid')),    
            array('value' =>'list' , 'label'=>$this->__('List'))     
          );
        break;
        case 'drop_down' :
          $options = array(
            array('value' =>'above'     , 'label'=>$this->__('Above Option')),         
            array('value' =>'before'    , 'label'=>$this->__('Before Option')),
            array('value' =>'below'     , 'label'=>$this->__('Below Option')),
            array('value' =>'swap'      , 'label'=>$this->__('Main Image')),
            array('value' =>'picker'    , 'label'=>$this->__('Color Picker')), 
            array('value' =>'pickerswap', 'label'=>$this->__('Picker & Main'))                   
          );
        break;       
        case 'multiple' :
          $options = array(
            array('value' =>'above', 'label'=>$this->__('Above Option')),         
            array('value' =>'below', 'label'=>$this->__('Below Option'))          
          );
        break;            
      }
      
      $select = $this->getLayout()->createBlock('adminhtml/html_select')
          ->setData(array(
              'id' => 'optionconfigurable_'.$option['type'].'_'.$option['id'].'_layout',
              'class' => 'select optionconfigurable-layout-select',
              'extra_params' => 'onchange="optionConfigurableImage.changePopup(\''.$option['type'].'\','.$option['id'].')"'                               
          ))
          ->setName('product[optionconfigurable_'.($option['type'] == 'a' ? 'attribute' : 'option').']['.$option['id'].'][layout]')
          ->setValue($option['layout'])             
          ->setOptions($options);

      return $select->getHtml();
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
      return count($this->getAttributes()) > 0;                         
    }     
    
         	  
	  
    public function getPriceValue($value, $isPercent)
    {
      if ((int)$value == 0)
        return '';        
      return $isPercent ? (float) $value : number_format($value, 2, null, '');
    }	  
	  
    public function getNoteDisabled($option)
    {
      return is_null($option['store_note']) && $this->getProduct()->getStoreId() > 0 ? 'disabled="disabled"' : '';
    }

    public function getDescriptionDisabled($value)
    {
      return is_null($value['store_description']) && $this->getProduct()->getStoreId() > 0 ? 'disabled="disabled"' : '';
    }

    public function getNoteShowHidden($option)
    {
      return is_null($option['store_note']) && $this->getProduct()->getStoreId() > 0 ? 'style="display:none"' : '';
    }

    public function getDescriptionShowHidden($value)
    {
      return is_null($value['store_description']) && $this->getProduct()->getStoreId() > 0 ? 'style="display:none"' : '';
    }
    	  
    public function getNoteScopeHtml($option)
    {
    
      if ($this->getProduct()->getStoreId() == 0)
        return '';
 
      $checked   = is_null($option['store_note']);
      $inputId   = "optionconfigurable_{$option['type']}_{$option['id']}_note";
      $type      = $option['type'] == 'a' ? 'attribute' : 'option';
      $inputName = "product[optionconfigurable_{$type}][{$option['id']}][scope][note]";             
      
      $checkbox  = '<br/><input type="checkbox" id="'. $inputId .'_use_default" class="product-option-scope-checkbox" name="'. $inputName .'" value="1" '. ($checked ? 'checked="checked"' : '') .'  onclick="optionConfigurableImage.setScope(this, \''. $inputId .'\')"/>'.
                   '<label class="normal" for="'. $inputId .'_use_default"> '. Mage::helper('adminhtml')->__('Use Default Value') .'</label>';
                    
      return $checkbox;
    }	  
	  

    public function getDescriptionScopeHtml($option, $value)
    {
    
      if ($this->getProduct()->getStoreId() == 0)
        return '';
 
      $checked    = is_null($value['store_description']);
      $inputId    = "optionconfigurable_{$option['type']}_{$value['id']}_description";
      $type       = $option['type'] == 'a' ? 'aoption' : 'value';
      $inputName  = "product[optionconfigurable_{$type}][{$value['id']}][scope][description]"; 
      
      $checkbox   = '<br/><input type="checkbox" id="'. $inputId .'_use_default" class="product-option-scope-checkbox" name="'. $inputName .'" value="1" '. ($checked ? 'checked="checked"' : '') .'  onclick="optionConfigurableImage.setScope(this, \''. $inputId .'\')"/>'.
                    '<label class="normal" for="'. $inputId .'_use_default"> '. Mage::helper('adminhtml')->__('Use Default Value') .'</label>';
                    
      return $checkbox;
    }
	  
	               
    public function getShowTextArea($option, $value = null)
    {
      if ($this->getIsWysiwygEnabled()){
        $wysiwygUrl = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product/wysiwyg');
        if (!is_null($value))
          $fieldId = 'optionconfigurable_'.$option['type'].'_'.$value['id'].'_description';
        else  
          $fieldId = 'optionconfigurable_'.$option['type'].'_'.$option['id'].'_note';      
        $extra = 'onclick="catalogWysiwygEditor.open(\''. $wysiwygUrl .'\', \''. $fieldId .'\')"';   
      } else {
        $extra = 'onclick="optionConfigurableImage.showTextArea(this)"';     
      } 

      return $extra;
    }	  
	  
	  
    public function getClickToEditText()
    {
        return $this->getIsWysiwygEnabled() ? Mage::helper('catalog')->__('WYSIWYG Editor') : $this->__('Click to edit');   
    }  
    
    public function getIsWysiwygEnabled()
    {
        $version = Mage::getVersion();
        return version_compare($version, '1.4.0.0') >= 0 && Mage::getSingleton('cms/wysiwyg_config')->isEnabled();
    } 
        	  
    public function getTabUrl()
    {
        return $this->getUrl('adminhtml/oc_options/optionimages', array('_current'=>true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }
        
    public function getTabLabel()
    {
        return $this->__('Option Images');
    }
        
    public function getTabTitle()
    {
        return $this->__('Option Images');
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
