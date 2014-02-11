<?php

class Pektsekye_OptionConfigurable_Model_Relation extends Mage_Core_Model_Abstract
{	
    protected $_relationRows;
    protected $_oc_relation_keys = array(      
          'aoption_to_attribute'=> array(0,'a','a'),
          'aoption_to_option'   => array(0,'a','o'),
          'value_to_attribute'  => array(0,'o','a'),
          'value_to_option'     => array(0,'o','o'),
          'aoption_to_aoption'  => array(1,'a','a'),
          'aoption_to_value'    => array(1,'a','o'),
          'value_to_aoption'    => array(1,'o','a'),
          'value_to_value'      => array(1,'o','o')
        );
          
                    
    public function _construct()
    {
        parent::_construct();
        $this->_init('optionconfigurable/relation');
    }


    public function getRelationData($product)
    {    
      $productId = $product->getId();
      $options = $this->getOptions($product);
    
      $default = array('a' => array(), 'o' => array());

      $valueIds             = $default;
      $valueIdsByOId        = $default;
      $optionIds            = $default;     
      $oIdByVId             = $default;
      $childrenOIdsByVId    = $default;
      $childrenOIdsByOId    = $default;
      $allChildrenOIdsByOId = $default;               
      $childrenVIdsByVId    = $default;
      $parentVIdsByOId      = $default;       
      $parentVIdsByVId      = $default;          
      $parentOIdByOId       = $default; 
      $parentOIdsByOId      = $default;

             
      foreach($options as $option){
        $t = $option['type'];
        $id = $option['id'];
        $optionIds[$t][] = $id;
        $valueIdsByOId[$t][$id] = array();        
        foreach($option['value_ids'] as $valueId){
          $valueIds[$t][] = $valueId;
          $oIdByVId[$t][$valueId] = $id;
          $valueIdsByOId[$t][$id][] = $valueId; 
        }        
      }    
      
      $rows = $this->getRelationRows($productId);
            
      foreach($this->_oc_relation_keys as $k => $t){
            
        foreach($rows[$k] as $r){
        
          $id  = (int) $r['id'];
          $cid = (int) $r['cid'];
          
          if (!in_array($id, $valueIds[$t[1]]))
            continue;
            
          if ($t[0] == 0){ 
            if (!in_array($cid, $optionIds[$t[2]]))
              continue;                   
            $childrenOIdsByVId[$t[1]][$id][$t[2]][] = $cid;            
            $parentVIdsByOId[$t[2]][$cid][$t[1]][] = $id;
            $cOId = $cid;
          } else {
            if (!in_array($cid, $valueIds[$t[2]]))
              continue;          
            $childrenVIdsByVId[$t[1]][$id][$t[2]][] = $cid;
            $parentVIdsByVId[$t[2]][$cid][$t[1]][] = $id;            
            $cOId = $oIdByVId[$t[2]][$cid];          
          }
          
          $oId = $oIdByVId[$t[1]][$id];
          
          if (!isset($childrenOIdsByOId[$t[1]][$oId][$t[2]]) || !in_array($cOId, $childrenOIdsByOId[$t[1]][$oId][$t[2]])){
            $childrenOIdsByOId[$t[1]][$oId][$t[2]][] = $cOId;
          }
             
          $parentOIdByOId[$t[2]][$cOId][$t[1]] = $oId;
        }
              
      }


      foreach($options as $option){
        $t = $option['type'];
        $id = $option['id'];      
        $parentOIdsByOId[$t][$id] = $this->getParentIds($t, $id, $parentOIdByOId);
        $children = $this->getChildrenIds($t, $id, $childrenOIdsByOId);
        if (count($children) > 0)
          $allChildrenOIdsByOId[$t][$id] = $children; 
      }


      $data = array(
        'valueIds'   => $valueIds,      
        'vIdsByOId'  => $valueIdsByOId,
        'optionIds'  => $optionIds,       
        'oIdByVId'   => $oIdByVId, 
        'cOIdsByVId' => $childrenOIdsByVId,
        'cOIdsByOId' => $allChildrenOIdsByOId,        
        'cVIdsByVId' => $childrenVIdsByVId,
        'pVIdsByOId' => $parentVIdsByOId,        
        'pVIdsByVId' => $parentVIdsByVId,
        'pOIdByOId'  => $parentOIdByOId,
        'pOIdsByOId' => $parentOIdsByOId                 
      );
      
      return $data;
    }


    public function getRelationRows($productId)
    {
      if (!isset($this->_relationRows))
        $this->_relationRows = $this->getResource()->getRelations($productId);
      
      return $this->_relationRows;
    }
    
    
    public function getRelations($productId)
    {        
      $relations = array();
      $rows = $this->getRelationRows($productId);
      foreach($rows as $k => $row){            
        foreach($row as $r){          
          $id  = (int) $r['id'];
          $cid = (int) $r['cid'];            
          $relations[$k][$id][] = $cid;
        }              
      }      
      return $relations;
    }
    
     
    public function getParentIds($type, $optionId, $parentOIdByOId, $ids = array('a'=>array(),'o'=>array()))
    {
      if (!isset($parentOIdByOId[$type][$optionId]))
        return $ids;
           
      $t = isset($parentOIdByOId[$type][$optionId]['a']) ? 'a' : 'o';      
      $id = $parentOIdByOId[$type][$optionId][$t];
      
      if (in_array($id, $ids[$t]))
        return $ids;
           
      $ids[$t][] = $id; 
      
      return $this->getParentIds($t, $id, $parentOIdByOId, $ids);
    }
 
 
     public function getChildrenIds($type, $optionId, $childrenOIdsByOId, $ids = array())
    {
      if (!isset($childrenOIdsByOId[$type][$optionId]))
        return $ids;
      
      foreach (array('a','o') as $t){           
        if (!isset($childrenOIdsByOId[$type][$optionId][$t]))
          continue; 
          
        foreach($childrenOIdsByOId[$type][$optionId][$t] as $id){                 
          if (isset($ids[$t]) && in_array($id, $ids[$t]))
            return $ids;
               
          $ids[$t][] = $id; 
          
          $ids = $this->getChildrenIds($t, $id, $childrenOIdsByOId, $ids);
        }
      }
      
      return $ids;
    }   
    
        
    public function saveRelationsData($productId, $data)
    {
      return $this->getResource()->saveRelationsData($productId, $data);
    }


    public function importRelations($originalProduct, $currentProductId, $importType)
    {       		     
      $data = array(
          'aoption_to_attribute'=> array(),
          'aoption_to_option'   => array(),
          'value_to_attribute'  => array(),
          'value_to_option'     => array(),
          'aoption_to_aoption'  => array(),
          'aoption_to_value'    => array(),
          'value_to_aoption'    => array(),
          'value_to_value'      => array()     
      );    
        
      if ($importType == 'attribute'){      
      
        $relationTypes = array('aoption_to_attribute','aoption_to_aoption');
        foreach($relationTypes as $relationType)                  
          $this->getResource()->copyRelation($originalProduct->getId(), $currentProductId, $relationType);    
                    
      } elseif ($importType == 'option'){  

        $rows = $this->getResource()->getRelations($originalProduct->getId());               
        $tIds = Mage::getModel('optionconfigurable/option')->getTranslatedIds($originalProduct, $currentProductId);
                
        foreach($rows['value_to_option'] as $r){
          $data['value_to_option'][$tIds[1][$r['id']]][] = $tIds[0][$r['cid']]; 
        }
            
        foreach($rows['value_to_value'] as $r){
          $data['value_to_value'][$tIds[1][$r['id']]][] = $tIds[1][$r['cid']];       
        }
                           
        $this->getResource()->saveRelationsData($currentProductId, $data);
                    
      } else { // importType == both
                        
        $rows = $this->getResource()->getRelations($originalProduct->getId());       
        $tIds = Mage::getModel('optionconfigurable/option')->getTranslatedIds($originalProduct, $currentProductId);        
        foreach($this->_oc_relation_keys as $k => $t){              
          foreach($rows[$k] as $r){          
            $id  = $t[1] == 'a' ? $r['id']  : $tIds[1][$r['id']];
            $cid = $t[2] == 'a' ? $r['cid'] : $tIds[$t[0]][$r['cid']];
            $data[$k][$id][] = $cid;                      
          }                
        }
              
        $this->getResource()->saveRelationsData($currentProductId, $data);
             
      }
                       
    }



    public function deleteAttributeValues($productId, $attributeIds, $attributeValueIds)
    {    
      $data = array(
          'aoption_to_attribute'=> array(),
          'aoption_to_option'   => array(),
          'value_to_attribute'  => array(),
          'value_to_option'     => array(),
          'aoption_to_aoption'  => array(),
          'aoption_to_value'    => array(),
          'value_to_aoption'    => array(),
          'value_to_value'      => array()     
      );
          
      $rows = $this->getResource()->getRelations($productId);              
      foreach($this->_oc_relation_keys as $k => $t){              
        foreach($rows[$k] as $r){
         
          if (($t[0] == 0 && (($t[1] == 'a' && in_array($r['id'], $attributeValueIds))
                           || ($t[2] == 'a' && in_array($r['cid'], $attributeIds))))
           || ($t[0] == 1 && (($t[1] == 'a' && in_array($r['id'], $attributeValueIds)) 
                           || ($t[2] == 'a' && in_array($r['cid'], $attributeValueIds))))){
            continue;                          
          }
                          
          $data[$k][$r['id']][] = $r['cid'];                                
        }                
      }
            
      $this->getResource()->saveRelationsData($productId, $data);
    }   
   
   
   
    public function saveCsvRelationData($productId, $rowIdRelations, $translatedIds)
    {
      $this->saveRelationsData($productId, $this->translateCsvRelation($rowIdRelations, $translatedIds));
    }    
    
    
    public function getTranslatedIds($ids)
    { 
      $tIds = array();
      
      if (isset($ids['attributes'])){
      
        $rows = Mage::getResourceModel('optionconfigurable/attribute')->getAttributeOptionIds(array_keys($ids['attributes']));
        $attributeIds = array();
        $optionIds = array();          
        foreach($rows as $row){
          $attributeIds[$row['attribute_code']] = $row['attribute_id'];
          $optionIds[$row['attribute_code']][] = $row['option_id']; 
        }           
        
        foreach($ids['attributes'] as $code => $attribute){
          foreach($attribute as $attributeId => $oIds){
            $tIds[0]['a'][$attributeId] = $attributeIds[$code];                     
            foreach($oIds as $k => $oId){
              if (!isset($optionIds[$code][$k]))
                break;
              $tIds[1]['a'][$oId] = $optionIds[$code][$k];        
            }
          }             
        }  
      }
      
      
      if (isset($ids['options'])){   
        $rows = Mage::getResourceModel('optionconfigurable/option')->getOptionIds(array_keys($ids['options']));

        $oIds = array();            
        foreach($rows as $row){            
          $oIds[$row['product_sku']][$row['option_id']][] = $row['value_id'];                                      
        }
        
        $optionIds = array();           
        $valueIds = array();          
        foreach($oIds as $productSku => $ovIds){ 
          foreach($ovIds as $optionId => $vIds){
            $optionIds[$productSku][] = $optionId;                        
            $valueIds[$productSku][] = $vIds;                     
          }                                       
        }

        foreach($ids['options'] as $productSku => $options){
          $i = 0;
          foreach($options as $optionId => $vIds){
            if (!isset($optionIds[$productSku][$i]))
              break;
            $tIds[0]['o'][$optionId] = $optionIds[$productSku][$i];
            foreach($vIds as $k => $vId){
              if (!isset($valueIds[$productSku][$i][$k]))
                break;             
              $tIds[1]['o'][$vId] = $valueIds[$productSku][$i][$k];
            }
            $i++;                          
          }
        }          
      }
        
      return $tIds;               
    }
    
        
    public function translateCsvRelation($rowIdRelations, $tIds)
    { 
      $relations = array();       
      foreach($this->_oc_relation_keys as $k => $t){
        $relations[$k] = array();
        if (isset($rowIdRelations[$k])){          
          foreach($rowIdRelations[$k] as $id => $cIds){
            foreach($cIds as $cId){
              if (isset($tIds[1][$t[1]][$id])){
                $vId = $tIds[1][$t[1]][$id];                            
                if ($t[0] == 0 && isset($tIds[0][$t[2]][$cId])){
                  $relations[$k][$vId][] = $tIds[0][$t[2]][$cId];                
                } elseif ($t[0] == 1 && isset($tIds[1][$t[2]][$cId])){
                  $relations[$k][$vId][] = $tIds[1][$t[2]][$cId];
                }                
              }
            }        
          }
        }  
      }                 
      return $relations;
    }               


    public function getOptions($product)
    {
      $options = array();      
                                                 
      if ($product->isConfigurable()){	
        $attributes = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);                
        foreach ($attributes as $attribute) {
            $id = (int) $attribute['attribute_id'];       
            $option = array(
                'type' => 'a',
                'id' => $id,                                                        
                'value_ids' => array()
            );      
            foreach ($attribute['values'] as $value)                                                    
              $option['value_ids'][] = (int) $value['value_index'];      
           $options[] = $option;          
        }        
      }
      
      foreach ($product->getOptions() as $_option) {
          $id = (int) $_option->getOptionId();
          $option = array(
              'type' => 'o',
              'id' => $id,                                                          
              'value_ids' => array()
          );                   
          foreach ($_option->getValues() as $value) 
            $option['value_ids'][] = (int) $value->getOptionTypeId();      
          $options[] = $option;
      }       
                           
      return $options;                               
    }


    public function getUsedOptionIds()
    { 
      $optionIds = array();
      $rows = Mage::getResourceModel('optionconfigurable/attribute')->getUsedAttributeOptionIds();
      foreach($rows as $row)
        $optionIds['attributes'][$row['attribute_code']][$row['attribute_id']][] = $row['option_id'];
      
      $rows = Mage::getResourceModel('optionconfigurable/option')->getUsedOptionIds();
      foreach($rows as $row){
        if ($row['value_id'] == null)
          $optionIds['options'][$row['sku']][$row['option_id']] = array(); 
        else   
          $optionIds['options'][$row['sku']][$row['option_id']][] = $row['value_id'];
      }
      
      return $optionIds;
    }
}
