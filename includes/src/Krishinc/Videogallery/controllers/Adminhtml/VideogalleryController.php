<?php
    class Krishinc_Videogallery_Adminhtml_VideogalleryController extends Mage_Adminhtml_Controller_Action
    {
        protected function _initAction()
        {
            $this->loadLayout()
                ->_setActiveMenu('videogallery/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Videogallery Manager'), Mage::helper('adminhtml')->__('Videogallery Manager'));
            return $this;
        }   
        public function indexAction() {
            $this->_initAction();       
            $this->_addContent($this->getLayout()->createBlock('videogallery/adminhtml_videogallery'));
            $this->renderLayout();
        }
         public function newAction() {
            $this->_forward('edit');
        }
        public function editAction()
        {
			
            $id = $this->getRequest()->getParam('id');
            $model = Mage::getModel('videogallery/videogallery')->load($id);

            if ($model->getVideogalleryId()) {
                $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if (!empty($data)) {
                    $model->setData($data);
                }
				
				Mage::register('videogallery_data', $model);

                $this->loadLayout();
                $this->_setActiveMenu('videogallery/items');

                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Videogallery Names Manager'), Mage::helper('adminhtml')->__('Videogallery Names Manager'));
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Videogallery Name'), Mage::helper('adminhtml')->__('Videogallery Name'));

                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                $this->_addContent($this->getLayout()->createBlock('videogallery/adminhtml_videogallery_edit'))
                        ->_addLeft($this->getLayout()->createBlock('videogallery/adminhtml_videogallery_edit_tabs'));

                $this->renderLayout();
            } else {
				$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
                if ($data) {
					print_r($data);exit;
                    //$data->getData();
                   // $model->setTitle($data['value']);
                    $model->setName($data['value']);
                    //$model->setName($data['value']);
                    //$model->setVideogalleryAttributeId($data['option_id']);
                    //$model->getVideogalleryAttributeId($data['option_id']);
                }
		
	            Mage::register('videogallery_data', $model);

                $this->loadLayout();
                $this->_setActiveMenu('videogallery/items');

                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Videogallery Name Manager'), Mage::helper('adminhtml')->__('Videogallery Names Manager'));
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Videogallery Name'), Mage::helper('adminhtml')->__('Videogallery Name'));

                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

                $this->_addContent($this->getLayout()->createBlock('videogallery/adminhtml_videogallery_edit'))
                        ->_addLeft($this->getLayout()->createBlock('videogallery/adminhtml_videogallery_edit_tabs'));

                $this->renderLayout();
            }
        }
       

       


    public function saveAction() {
    	
        if ($data = $this->getRequest()->getPost()) {
			//echo "<pre>";
			//print_r($data);
			$url = $data['videogallery_url'];
			$checkurl = explode('?v=',$url);
			if($checkurl[0] != 'https://www.youtube.com/watch' && $checkurl[0] != 'http://www.youtube.com/watch')
			{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('videogallery')->__('Please Enter Valid Youtube Url For Example : https://www.youtube.com/watch?v=lTEjfMjv654'));
			if ($this->getRequest()->getParam('id')) {
				Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
				$this->_redirect('*/*/');
				return;
			
			}else{
			parse_str( parse_url( $url, PHP_URL_QUERY ) );
			$videourl = 'http://img.youtube.com/vi/'.$v.'/0.jpg';
			$videoimage = $v;
			$content = file_get_contents("http://youtube.com/get_video_info?video_id=".$videoimage);
			parse_str($content, $videoname);
			$videoname = $videoname['title'];
			$videoname;
			if(isset($data['videogallery_url']) && $data['videogallery_url'] != '') {
									if(!file_exists(Mage::getBaseDir('media').'/videogallery/'))mkdir(Mage::getBaseDir('media').'/videogallery/',0777);
									$img_file = $videourl;				
									$img_file=file_get_contents($img_file);
									$file_loc=Mage::getBaseDir('media').DS."videogallery".DS.'videogallery_'.$videoimage.'.jpg';
									
									$file_handler=fopen($file_loc,'w');
									
									if(fwrite($file_handler,$img_file)==false){
										echo 'error';
									}
									fclose($file_handler);
									
								 $newfilename ='videogallery_'.$videoimage.'.jpg';
								 // Upload the image
								 $videoimage = $newfilename;
							
			}
			
			
			if($this->getRequest()->getParam('id'))
            {	
				$modeldata = Mage::getModel('videogallery/videogallery')->getCollection()->addFieldToFilter('videogallery_id',array('eq'=>$this->getRequest()->getParam('id')));
				$model = Mage::getModel('videogallery/videogallery')->load($modeldata->getFirstItem()->getVideogalleryId());
				$model->setImage($videoimage)->setName($videoname)->setVideogalleryUrl($data['videogallery_url'])->setVideogalleryCategory($data['videogallery_category']);
            }else {
				$model = Mage::getModel('videogallery/videogallery');
                $model->setData($data)->setImage($videoimage)->setName($videoname)->setVideogalleryUrl($data['videogallery_url'])->setVideogalleryCategory($data['videogallery_category']);
	        }
			
            try {
                $model->save();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('videogallery')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('videogallery_id' => $model->getVideogalleryId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('videogallery_id' => $this->getRequest()->getParam('videogallery_id')));
                return;
            }
			}
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('videogallery')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
        
    }
       
        public function deleteAction()
        {
            
                  if ($this->getRequest()->getParam('videogallery_id') > 0) {
                        try {
                            $model = Mage::getModel('videogallery/videogallery')->load($this->getRequest()->getParam('videogallery_id'), 'videogallery_id');
                            $image = $model->getImage();
                            $filepath = Mage::getBaseDir('media').DS."videogallery\\".$image;
							$filepath2 = Mage::getBaseDir('media').DS."videogallery".DS."resized".DS."small\\".$image;
							$filepath3 = Mage::getBaseDir('media').DS."videogallery".DS."resized".DS."thumb\\".$image;
                            unlink($filepath);
							unlink($filepath2);
							unlink($filepath3);
							if ($model->getVideogalleryId()) {
                                $model->delete();
                            }  
                        } catch (Exception $e) {
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Item could not be deleted'));
                            $this->_redirect('*/*/');
                        }
                  }
        $this->_redirect('*/*/');
        }
        public function massDeleteAction() {
            $videogalleryIds = $this->getRequest()->getParam('videogallery');
            
            if (!is_array($videogalleryIds)) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
            } else {
                try {
                    foreach ($videogalleryIds as $videogalleryId) {
                        $model = Mage::getModel('videogallery/videogallery')->load($videogalleryId, 'videogallery_id');
                        $image = $model->getImage();
                        $filepath = Mage::getBaseDir('media').DS."videogallery\\".$image;
						$filepath2 = Mage::getBaseDir('media').DS."videogallery".DS."resized".DS."small\\".$image;
						$filepath3 = Mage::getBaseDir('media').DS."videogallery".DS."resized".DS."thumb\\".$image;
                        unlink($filepath);
						unlink($filepath2);
						unlink($filepath3);
						if ($model->getVideogalleryId()) {
                            $model->delete();
                        }
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('adminhtml')->__(
                                    'Total of %d record(s) were successfully deleted', count($videogalleryIds)
                            )
                    );
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            $this->_redirect('*/*/index');
        }
         public function exportCsvAction() {
            $fileName = 'videogallery.csv';
            $content = $this->getLayout()->createBlock('videogallery/adminhtml_videogallery_grid')
                    ->getCsv();

            $this->_sendUploadResponse($fileName, $content);
        }

        public function exportXmlAction() {
            $fileName = 'videogallery.xml';
            $content = $this->getLayout()->createBlock('videogallery/adminhtml_videogallery_grid')
                    ->getXml();

            $this->_sendUploadResponse($fileName, $content);
        }

        protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
            $response = $this->getResponse();
            $response->setHeader('HTTP/1.1 200 OK', '');
            $response->setHeader('Pragma', 'public', true);
            $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
            $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
            $response->setHeader('Last-Modified', date('r'));
            $response->setHeader('Accept-Ranges', 'bytes');
            $response->setHeader('Content-Length', strlen($content));
            $response->setHeader('Content-type', $contentType);
            $response->setBody($content);
            $response->sendResponse();
            die;
        }
        /**
         * Product grid for AJAX request.
         * Sort and filter result for example.
         */
        public function gridAction()
        {
            $this->loadLayout();
            $this->getResponse()->setBody(
                   $this->getLayout()->createBlock('krishinc/adminhtml_videogallery_grid')->toHtml()
            );
        }
       
    }