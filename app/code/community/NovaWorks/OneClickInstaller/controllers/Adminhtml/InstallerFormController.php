<?php

class NovaWorks_OneClickInstaller_Adminhtml_InstallerFormController extends Mage_Adminhtml_Controller_Action
{
    protected $_storeId = null;

    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
		protected function deleteBlock($id){
	        $block = Mage::getModel('cms/block')
	                ->setStoreId($this->_storeId)
	                ->load($id);
	
			$block->delete();
		}
    
    public function uninstallAction()
    {
		$post = $this->getRequest()->getPost();
		$message = "";
        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
			$storeId = $post['design']['store_id'];		
			$this->_storeId = $storeId;	
			
			$this->deleteBlock('block_custom_menu');
			$this->deleteBlock('block_custom_slidebar_2');
			$this->deleteBlock('block_custom_slidebar_3');
			$this->deleteBlock('aditional_footer_left');
			$this->deleteBlock('block_footer_right');
			$this->deleteBlock('block_contact_comment');
			$this->deleteBlock('block_contact_left');
			$this->deleteBlock('block_contact_map');
			$this->deleteBlock('block_empty_center');
			$this->deleteBlock('block_empty_center');
			if($storeId == 0) {
				$scope = 'default';
			}else{
				$scope = 'stores';
			}
			Mage::getConfig()->saveConfig('design/package/name','default', $scope, $storeId);
			Mage::getConfig()->saveConfig('design/theme/template', 'default', $scope, $storeId);
			Mage::getConfig()->saveConfig('design/theme/skin', 'default', $scope, $storeId);
			Mage::getConfig()->saveConfig('design/theme/layout', 'default', $scope, $storeId);
			Mage::getConfig()->saveConfig('design/theme/default', 'default', $scope, $storeId);
			
			$message = $this->__('avena theme was uninstalled successfully. ');
			Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
		$this->_redirect('*/*');		
    }
    public function installAction()
    {
      $post = $this->getRequest()->getPost();
      $message = "";
      try {
      	if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
        }
				$storeId 			= $post['design']['store_id'];
				$InstallBlock 	= $post['design']['install_block'];
				$InstallSlideshow 	= $post['design']['install_slideshow'];
				$stores = array($storeId); 	//Used at all blocks
				$RootCategoryId = Mage::app()->getStore($storeId)->getRootCategoryId();			
				$novaworks_uploaded = false;
				$design = Mage::getModel('core/design_package')->getPackageList();
				foreach ($design as $package){
					if($package == "novaworks") {
						$novaworks_uploaded = true;
						break;
					}
				}
				if (!$novaworks_uploaded){
					Mage::throwException($this->__('Avena Theme was not found. Please upload the theme first.'));				
				}					
				if($storeId == 0) {
					$scope = 'default';
				}else{
					$scope = 'stores';
				}
				//Configuration 
				//Design
				Mage::getConfig()->saveConfig('design/package/name', "novaworks", $scope, $storeId);
				Mage::getConfig()->saveConfig('design/theme/template', "avena", $scope, $storeId);
				Mage::getConfig()->saveConfig('design/theme/skin', "avena", $scope, $storeId);		
				Mage::getConfig()->saveConfig('design/theme/layout', "avena", $scope, $storeId);
				Mage::getConfig()->saveConfig('design/theme/default', "avena", $scope, $storeId);
				//Coppyright
				Mage::getConfig()->saveConfig('design/footer/copyright', "&copy; 2013 Avena Theme. All Rights Reserved. Designed by <a href=\"http://novaworks.net/\" title=\"Novaworks\">Novaworks</a>",$scope, $storeId);
				//Header
				Mage::getConfig()->saveConfig('design/header/logo_src', "images/logo.png", $scope, $storeId);
				Mage::getConfig()->saveConfig('web/default/cms_home_page', "home_v1", $scope, $storeId);
				//Setup Static Block
				if($InstallBlock == 1) {


					// Block Custom Menu
					$content = '<div class="col4-set">
<div class="col-1">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce suscipit bibendum risus, eget faucibus sapien cursus quis. Integer ultrices tempor sapien, quis mollis elit ornare at.</p>
</div>
<div class="col-2">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce suscipit bibendum risus, eget faucibus sapien cursus quis. Integer ultrices tempor sapien, quis mollis elit ornare at.</p>
</div>
<div class="col-3">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce suscipit bibendum risus, eget faucibus sapien cursus quis. Integer ultrices tempor sapien, quis mollis elit ornare at.</p>
</div>
<div class="col-4">
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce suscipit bibendum risus, eget faucibus sapien cursus quis. Integer ultrices tempor sapien, quis mollis elit ornare at.</p>
</div>
</div>';
					$data = array("title" => "Block Custom", 
								  "identifier" => "block_custom_menu",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__('Custom Menu block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}				
					// Custom Slidebar 2
					$content = '<div class="block custom-html">
<div id="custom-html"><strong> <span>Custom HTML</span> </strong></div>
<div class="block-content">
<p>Lorem ipsum dolor sit amet, con-sectetuer adipiscing elit sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat.d tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim vemodo consequat.</p>
<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat</p>
</div>
</div>';
					$data = array("title" => "Custom Slidebar 2", 
								  "identifier" => "block_custom_slidebar_2",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Custom Slidebar 2 block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}	
					// Custom Slidebar 3
					$content = '<div class="left-block-inner">
<ul class="slides">
<li>
<p style="text-align: center;"><img src="{{media url="wysiwyg/detail-sample2_2.jpg"}}" alt="" /></p>
</li>
<li>
<p style="text-align: center;"><img src="{{media url="wysiwyg/detail-sample2_2.jpg"}}" alt="" /></p>
</li>
<li>
<p style="text-align: center;"><img src="{{media url="wysiwyg/detail-sample2_2.jpg"}}" alt="" /></p>
</li>
</ul>
</div>';
					$data = array("title" => "Custom Right Slidebar", 
								  "identifier" => "block_custom_slidebar_3",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Custom Slidebar 2 block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}	
					
					
																								
					//Bottom menu
					$content = '<div class="span6 f-col f-col1">
<h4>Your Account</h4>
<ul class="bottom-menu">
<li class="first"><a href="#">Your Account</a></li>
<li class="item"><a href="#">Personal information</a></li>
<li class="item"><a href="#">Addresses</a></li>
<li class="item"><a href="#">Discount</a></li>
<li class="last"><a href="#">Orders history</a></li>
</ul>
</div>
<div class="span6 f-col f-col2">
<h4>Our Offers</h4>
<ul class="bottom-menu">
<li class="first"><a href="#">New products</a></li>
<li class="item"><a href="#">Top sellers</a></li>
<li class="item"><a href="#">Specials</a></li>
<li class="item"><a href="#">Manufacturers</a></li>
<li class="last"><a href="#">Suppliers</a></li>
</ul>
</div>';
					$data = array("title" => "Bottom menu", 
								  "identifier" => "aditional_footer_left",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Bottom menu block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}		
																			
//Block Footer Right
					$content = '
<ul class="maiNav">
<li class="title"><a href="#">[icon name="icon-facebook"]</a></li>
<li class="title"><a href="#">[icon name="icon-twitter"]</a></li>
<li class="title"><a href="#">[icon name="icon-flickr"]</a></li>
<li class="title"><a href="#">[icon name="icon-rss"]</a></li>
<li class="title"><a href="#">[icon name="icon-linkedin"]</a></li>
<li class="title"><a href="#">[icon name="icon-mail"]</a></li>
<li class="title last"><a href="#">[icon name="icon-skype-1"]</a></li>
</ul>';
					$data = array("title" => "Social Icon", 
								  "identifier" => "block_footer_right",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Bottom menu block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}	
																			
//Block Contact Comment
					$content = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut odio tortor, tristique sit amet suscipit eu, convallis vitae purus. Integer egestas massa sit amet mauris fringilla vehicula bibendum nullahendrerit. Vivamus tincidunt rhoncus urna malesuada imperdiet. Maecenas ullamcorper imperdiet estnon luctus. Mauris diam augue, ullamcorper sit amet.</p>';
					$data = array("title" => "Block Contact Comment", 
								  "identifier" => "block_contact_comment",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Bottom menu block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}												
	// Block contact left
					$content = '<div class="row-fluid will-update">
<h3>Contact Details</h3>
<ul>
<li class="telephone">[icon name="icon-phone"]
<p>0203 280 3703</p>
<p>0203 281 37034</p>
</li>
<li class="mobile">[icon name="icon-mobile"]
<p>445-115-747-38</p>
<p>445-170-029-32</p>
</li>
<li class="email">[icon name="icon-mail"]
<p>Avena@gmail.com</p>
<p>Avena@aol.com</p>
</li>
<li class="skype">[icon name="icon-skype"]
<p>Avena_store</p>
<p>Avena_support</p>
</li>
</ul>
</div>
<div class="row-fluid will-update">
<h3>Address</h3>
<ul>
<li><a>United Kingdom</a></li>
<li><a>Greater London</a></li>
<li><a>London 02587</a></li>
<li><a>Oxford Street 48/188</a></li>
<li><a>Working days: Mon. - Sun.</a></li>
<li><a>Working hours: 9.00-8.00PM</a></li>
</ul>
</div>';
					$data = array("title" => "Block Contact Left", 
								  "identifier" => "block_contact_left",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block Contact Top created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}		
	// Block contact map
					$content = '<h3>Maps</h3>
<p><img src="{{media url="wysiwyg/map.jpg"}}" alt="" /></p>';
					$data = array("title" => "Block Contact Map", 
								  "identifier" => "block_contact_map",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block Contact Bottom created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}
	
// Block Empty Center
					$content = '<p>* This is a static CMS block displayed if category is empty.</p>';
					$data = array("title" => "Block Empty Center", 
								  "identifier" => "block_empty_center",
								  "stores" => $stores, 
								  "is_active" => 1, 
								  "content" => $content);
					$model = Mage::getModel('cms/block'); // loads cms/block model
					$model->setData($data); // add data to a model
		      try {
						$model->save();				      
						$message .= $this->__(' Block created.');
		      } catch (Exception $e){
						Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
						$this->_redirect('*/*');
						return;
					}
										
																					
         //End Setup Static Block					
				}
				
							
				
				$model = Mage::getModel('core/store');
				$storeName = Mage::getModel('core/store')->load($storeId)->getName();
				$storeCode = Mage::getModel('core/store')->load($storeId)->getCode();
				$store = Mage::app()->getStore($storeId);
			
				$message = $this->__('avena Theme was successfully installed on <i>'.$storeName.'</i>!');
        Mage::getSingleton('adminhtml/session')->addSuccess($message);
      } catch (Exception $e) {
        Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
      }
      $this->_redirect('*/*');
		}    
}