<?php class NovaWorks_ThemeOptions_Model_Productstypes
{
    public function toOptionArray()
    {
		$category = Mage::getModel('catalog/category'); 
        $tree = $category->getTreeModel(); 
        $tree->load();
        $ids = $tree->getCollection()->getAllIds(); 
		$arr = array();
        
if ($ids){ 
foreach ($ids as $id){ 
$cat = Mage::getModel('catalog/category'); 
$cat->load($id);
$children = $cat->getProductCount();
if($children):
$arr[$id] = $cat->getName();
endif;
}}
$arr['new'] = array('value'=>'new', 'label'=>Mage::helper('themeoptions')->__('New Products'));
$arr['bestsellers'] = array('value'=>'bestsellers', 'label'=>Mage::helper('themeoptions')->__('Bestsellers'));
        return $arr;
    }

}?>