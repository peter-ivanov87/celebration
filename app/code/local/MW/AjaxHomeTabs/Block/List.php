<?php
class MW_AjaxHomeTabs_Block_List extends Mage_Catalog_Block_Product_List
{		
	private $tabs =array();
	private $catfeatured = NULL;
	private $default_tab = "";
	private $feature_tab = 0;
	private $type = "";
	private $time_cache = 0;
	
	private function _getCacheTags()
	{
		return $this->getRequest()->getParam('type').'_'.$this->getRequest()->getParam('mode');
	}
	
//    public function stripTags($test){
//    	return $test;
//    }
//    ham nay gay bug
	protected function _construct(){
	
		// type of product collection
		$this->type = $this->getRequest()->getParam("type");
		if (!$this->type) 
            $this->type = Mage::getStoreConfig('ajaxhometabs/tabgeneral/default_tab');
		
		$cache = Mage::getStoreConfig('ajaxhometabs/tabgeneral/allow_cache');
		
		(empty($cache) || !is_numeric($cache)) ? 0 : $cache;
		
		if($cache > 0 ){		
			$this->$time_cache = $time = $cache * 60;
			$this->addData(
				array('cache_lifetime'=>$time,
						'cache_tags'=>array($this->_getCacheTags()),
						'cache_key'            => 'key_'.$this->_getCacheTags(),
				)
			);
		}
	}
	
	
	public function getColumnCount(){
		$cols = Mage::getStoreConfig('ajaxhometabs/tabgeneral/grid_number_columns');
		$cols = (empty($cols) || !is_numeric($cols)) ? 3 : $cols;
		return $cols;
	}
	
	public function getLimitOf($tab){
		$limit = Mage::getStoreConfig('ajaxhometabs/'.$tab.'/limit_view');
		$curr_mode_tab  = Mage::getStoreConfig('ajaxhometabs/'.$tab.'/view_mode');
		
		
		if(empty($limit)){	
			if($curr_mode_tab == "grid"){
				$limit = Mage::getStoreConfig('ajaxhometabs/tabgeneral/default_limit_grid');
			}else{
				$limit = Mage::getStoreConfig('ajaxhometabs/tabgeneral/default_limit_list');
			}
		}
		return $limit;
	}
	
	public function getStore(){
		// get current store
        return Mage::app()->getStore()->getId();
	}
	
	public function gettabtype(){
		return $this->type;
	}
	
	// String type
	public function _getProductCollection(){
	$this->_productCollection=null;
        
        
        if (!$this->_productCollection) {		
    
		 switch($this->type){
				case "toprate":
						$this->_productCollection =   $this->topRate($this->getLimitOf($this->type));
					break;
				 case "topreview":
						$this->_productCollection =  $this->topReview($this->getLimitOf($this->type));
					 break;
				 case "topnewest":
						$this->_productCollection =  $this->topLasttest($this->getLimitOf($this->type));
					 break;
				case "topwish":
						$this->_productCollection =  $this->topWishlist($this->getLimitOf($this->type));
					break;
				case "topfeature":
						$this->_productCollection =  $this->topFeatured($this->getLimitOf($this->type));
					break;
				case "custom1":
				case "custom2":
						$this->_productCollection =  $this->custom($this->type, $this->getLimitOf($this->type));
					break;
					
				default:
						$this->_productCollection =  $this->topBestSell($this->getLimitOf($this->type));
					break;
		 }

		} 
		return $this->_productCollection;
	}
	
	
	public function custom($type, $limit){
		$custom_cat = Mage::getStoreConfig('ajaxhometabs/'.$type.'/catalog_id');
		
		(empty($custom_cat) || ! is_numeric($custom_cat)) ? 0 : $custom_cat;
		
		$collection = $this->getProductCollect();
		
		if($custom_cat > 0){
		
			$collection->addCategoryFilter(Mage::getModel('catalog/category')->load($custom_cat));
			$collection
				->setPageSize($limit)
				->setOrder('created_at', 'desc')
				->load();
		}else{
			$collection->addFieldToFilter('entity_id', 0);
		}
		
		return $collection;
	}
	
	

	public function topRate($limit){		
		 $collection = Mage::getResourceModel('reports/review_product_collection')
            ->joinReview();
		$collection->getSelect()
		->order('avg_rating desc');		
		$collection->setPageSize($limit);	
			 return $collection;
	}
	
	public function topReview($limit){
		
		 $collection = Mage::getResourceModel('reports/review_product_collection')
            ->joinReview();
		$collection->getSelect()
		->order('review_cnt desc');	
		$collection->setPageSize($limit);		
			 return $collection;
	}
	
    public function topWishlist($limit){
		
		$collection = $this->getProductCollect();
			
		$collection->getSelect()
			 ->join(array('wi'=>'wishlist_item'), 'wi.product_id = e.entity_id', array('wi.qty','count'=>'sum(wi.qty)'))
			 ->group('wi.product_id')
			->order('count desc')			
			 ->where('wi.store_id ='.$this->getStore())			
			 ;
		$collection
			->addAttributeToSelect('*')
			->setPageSize($limit)
			->load()
			;
			//echo $collection->getSelect();die;
		return $collection;
	}
	
	
	public function topFeatured($limit){
		$this->feature_tab =  Mage::getStoreConfig('ajaxhometabs/topfeature/feature_catalog');
                //var_dump($this->feature_tab);
                //die();
		(empty($this->feature_tab) || !is_numeric($this->feature_tab)) ? 0 : $this->feature_tab; 
			$visibility = array(
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                  );
				  
		
                        $collection = Mage::getModel('catalog/product')
                                ->getCollection()
                                ->addAttributeToSort()
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('visibility',$visibility)
                                ->addCategoryFilter(Mage::getModel('catalog/category')->load($this->feature_tab))
                                ->addStoreFilter($this->getStore())
                                ->addMinimalPrice()
					->addFinalPrice()
					->setPageSize($limit);
                  
				$collection->getSelect()
                                        ->order(new Zend_Db_Expr('RAND()'));
						//->join(array('ccp'=>'catalog_category_product'), 'e.entity_id = ccp.product_id')
                                            	//->where('category_id='.$this->feature_tab);
                               //$collection->load();
					
			return $collection;
	}
	
	
	public function topLasttest($limit){
		$visibility = array(
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                  );
		$collection = Mage::getModel('catalog/product')
					->getCollection()
					->addAttributeToSelect('*')
					->addAttributeToFilter('visibility',$visibility)
					->setOrder('created_at','DESC')
					->addStoreFilter($this->getStore())
					->addMinimalPrice()
					->addFinalPrice()
					->setPageSize($limit)
					->load()
					;
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		return $collection;
	}
	
	// get Top best sell
	 public function topBestSell($limit){

          /*   $storeId = Mage::app()->getStore()->getId();
            $_productCollection = Mage::getResourceModel('reports/product_collection')
                              ->addAttributeToSelect('*')
                              ->addAttributeToFilter('type_id', 'configurable')
                              ->addOrderedQty()
                            ->setPageSize($limit);
            
            $productId=array();
            
            foreach($_productCollection->getItems() as $key=>$object){
                $_product=Mage::getModel('catalog/product')->load($object->getId());                
                echo ($_product->getId()."->");
                if ($_product->getId()) { //valid ID
                    
                   $productId[]=$object->getId();
                }
                else{
                    $_productCollection->removeItemByKey($key);
                }
               
                
          //    var_dump($product->ordered_qty);
              /*if(!isset($parentProducts[$parents[0]]))
                  $parentProducts[$parents[0]]=0;
              $parentProducts[$parents[0]] += (int)$product->ordered_qty;*/
       //     }
        
         //   return $_productCollection;
            
            
            
            
		$collection = Mage::getResourceModel('sales/report_bestsellers_collection')
            ->setModel('catalog/product')
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->setPageSize($limit)
        ;        
                 
        $productIds=array();
        foreach ($collection as $object){			        
                        
                $configurableProduct = Mage::getModel('catalog/product_type_configurable');
                $parentIdArray = $configurableProduct->getParentIdsByChild($object->getProductId());

                    //if simple product belong to configurable product
                    if (!empty($parentIdArray))
                    {
                        $parentProduct = Mage::getModel('catalog/product')->load($parentIdArray[0]);
                         if($parentProduct->getTypeId() == 'configurable' )
                        {
                            if (in_array($parentProduct->getEntityId(),$productIds)==false ){
                                 $productIds[]=$parentProduct->getEntityId();
                            }
                                 continue;
                        };
                        
                   }       
                 $productIds[]=$object->getProductId();
        };	     
        
        $best_seller_collection = Mage::getModel('catalog/product')->getCollection()
                 ->addAttributeToSelect('*')
                ->addAttributeToFilter('entity_id', array('in' => $productIds));
		return $best_seller_collection;	
	 }
	 
	/* Retrieve data
	* return collection Product
	*/
	private function getProductCollect(){
		$visibility = array(
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                      Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
                  );
		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('*')
			->addStoreFilter($this->getStore())
			->addAttributeToFilter('visibility',$visibility)
			->addMinimalPrice()
			->addFinalPrice()
			//->setPageSize($this->getLimit())
			;
		return $collection;
	}

}